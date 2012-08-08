<?php
/**
*
* @package search
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @ignore
*/
define('SPHINX_MAX_MATCHES', 20000);
define('SPHINX_CONNECT_RETRIES', 3);
define('SPHINX_CONNECT_WAIT_TIME', 300);

/**
* fulltext_sphinx
* Fulltext search based on the sphinx search deamon
* @package search
*/
class phpbb_search_fulltext_sphinx
{
	private $stats = array();
	private $split_words = array();
	private $id;
	private $indexes;
	private $sphinx;
	private $phpbb_root_path;
	private $php_ext;
	private $auth;
	private $config;
	private $db;
	private $db_tools;
	private $dbtype;
	private $user;
	private $config_file_data = '';
	public $search_query;
	public $common_words = array();

	/**
	 * Constructor
	 * Creates a new phpbb_search_fulltext_postgres, which is used as a search backend.
	 *
	 * @param string|bool $error Any error that occurs is passed on through this reference variable otherwise false
	 */
	public function __construct(&$error, $phpbb_root_path, $phpEx, $auth, $config, $db, $user)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;
		$this->config = $config;
		$this->user = $user;
		$this->db = $db;
		$this->auth = $auth;

		if (!class_exists('phpbb_db_tools'))
		{
			require($this->phpbb_root_path . 'includes/db/db_tools.' . $this->php_ext);
		}
		
		// Initialize phpbb_db_tools object
		$this->db_tools = new phpbb_db_tools($this->db);

		if(!$this->config['fulltext_sphinx_id'])
		{
			set_config('fulltext_sphinx_id', unique_id());
		}
		$this->id = $this->config['fulltext_sphinx_id'];
		$this->indexes = 'index_phpbb_' . $this->id . '_delta;index_phpbb_' . $this->id . '_main';

		if (!class_exists('SphinxClient'))
		{
			require($this->phpbb_root_path . 'includes/sphinxapi.' . $this->php_ext);
		}

		// Initialize sphinx client
		$this->sphinx = new SphinxClient();

		$this->sphinx->SetServer(($this->config['fulltext_sphinx_host'] ? $this->config['fulltext_sphinx_host'] : 'localhost'), ($this->config['fulltext_sphinx_port'] ? (int) $this->config['fulltext_sphinx_port'] : 9312));

		$error = false;
	}
	
	/**
	* Returns the name of this search backend to be displayed to administrators
	*
	* @return string Name
	*
	* @access public
	*/
	public function get_name()
	{
		return 'Sphinx Fulltext';
	}

	/**
	* Checks permissions and paths, if everything is correct it generates the config file
	*
	* @return string|bool Language key of the error/incompatiblity encountered, or false if successful
	*
	* @access public
	*/
	function init()
	{
		if ($this->db->sql_layer != 'mysql' && $this->db->sql_layer != 'mysql4' && $this->db->sql_layer != 'mysqli' && $this->db->sql_layer != 'postgres')
		{
			return $this->user->lang['FULLTEXT_SPHINX_WRONG_DATABASE'];
		}

		// Move delta to main index each hour
		set_config('search_gc', 3600);

		return false;
	}

	/**
	 * Generates content of sphinx.conf
	 *
	 * @return bool True if sphinx.conf content is correctly generated, false otherwise
	 *
	 * @access private
	 */
	function config_generate()
	{
		// Check if Database is supported by Sphinx
		if ($this->db->sql_layer =='mysql' || $this->db->sql_layer == 'mysql4' || $this->db->sql_layer == 'mysqli')
		{
			$this->dbtype = 'mysql';
		}
		else if ($this->db->sql_layer == 'postgres')
		{
			$this->dbtype = 'pgsql';
		}
		else
		{
			$this->config_file_data = $this->user->lang('FULLTEXT_SPHINX_WRONG_DATABASE');
			return false;
		}

		// Check if directory paths have been filled
		if (!$this->config['fulltext_sphinx_data_path'])
		{
			$this->config_file_data = $this->user->lang('FULLTEXT_SPHINX_NO_CONFIG_DATA');
			return false;
		}

		include($this->phpbb_root_path . 'config.' . $this->php_ext);

		/* Now that we're sure everything was entered correctly,
		generate a config for the index. We use a config value
		fulltext_sphinx_id for this, as it should be unique. */
		$config_object = new phpbb_search_sphinx_config($this->config_file_data);
		$config_data = array(
			'source source_phpbb_' . $this->id . '_main' => array(
				array('type',						$this->dbtype),
				// This config value sql_host needs to be changed incase sphinx and sql are on different servers
				array('sql_host',					$dbhost),
				array('sql_user',					$dbuser),
				array('sql_pass',					$dbpasswd),
				array('sql_db',						$dbname),
				array('sql_port',					$dbport),
				array('sql_query_pre',				'SET NAMES \'utf8\''),
				array('sql_query_pre',				'UPDATE ' . SPHINX_TABLE . ' SET max_doc_id = (SELECT MAX(post_id) FROM ' . POSTS_TABLE . ') WHERE counter_id = 1'),
				array('sql_query_range',			'SELECT MIN(post_id), MAX(post_id) FROM ' . POSTS_TABLE . ''),
				array('sql_range_step',				'5000'),
				array('sql_query',					'SELECT
						p.post_id AS id,
						p.forum_id,
						p.topic_id,
						p.poster_id,
						CASE WHEN p.post_id = t.topic_first_post_id THEN 1 ELSE 0 END as topic_first_post,
						p.post_time,
						p.post_subject,
						p.post_subject as title,
						p.post_text as data,
						t.topic_last_post_time,
						0 as deleted
					FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
					WHERE
						p.topic_id = t.topic_id
						AND p.post_id >= $start AND p.post_id <= $end'),
				array('sql_query_post',				''),
				array('sql_query_post_index',		'UPDATE ' . SPHINX_TABLE . ' SET max_doc_id = $maxid WHERE counter_id = 1'),
				array('sql_query_info',				'SELECT * FROM ' . POSTS_TABLE . ' WHERE post_id = $id'),
				array('sql_attr_uint',				'forum_id'),
				array('sql_attr_uint',				'topic_id'),
				array('sql_attr_uint',				'poster_id'),
				array('sql_attr_bool',				'topic_first_post'),
				array('sql_attr_bool',				'deleted'),
				array('sql_attr_timestamp'	,		'post_time'),
				array('sql_attr_timestamp'	,		'topic_last_post_time'),
				array('sql_attr_str2ordinal',		'post_subject'),
			),
			'source source_phpbb_' . $this->id . '_delta : source_phpbb_' . $this->id . '_main' => array(
				array('sql_query_pre',				''),
				array('sql_query_range',			''),
				array('sql_range_step',				''),
				array('sql_query',					'SELECT
						p.post_id AS id,
						p.forum_id,
						p.topic_id,
						p.poster_id,
						CASE WHEN p.post_id = t.topic_first_post_id THEN 1 ELSE 0 END as topic_first_post,
						p.post_time,
						p.post_subject,
						p.post_subject as title,
						p.post_text as data,
						t.topic_last_post_time,
						0 as deleted
					FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
					WHERE
						p.topic_id = t.topic_id
						AND p.post_id >=  ( SELECT max_doc_id FROM ' . SPHINX_TABLE . ' WHERE counter_id=1 )'),
			),
			'index index_phpbb_' . $this->id . '_main' => array(
				array('path',						$this->config['fulltext_sphinx_data_path'] . 'index_phpbb_' . $this->id . '_main'),
				array('source',						'source_phpbb_' . $this->id . '_main'),
				array('docinfo',					'extern'),
				array('morphology',					'none'),
				array('stopwords',					''),
				array('min_word_len',				'2'),
				array('charset_type',				'utf-8'),
				array('charset_table',				'U+FF10..U+FF19->0..9, 0..9, U+FF41..U+FF5A->a..z, U+FF21..U+FF3A->a..z, A..Z->a..z, a..z, U+0149, U+017F, U+0138, U+00DF, U+00FF, U+00C0..U+00D6->U+00E0..U+00F6, U+00E0..U+00F6, U+00D8..U+00DE->U+00F8..U+00FE, U+00F8..U+00FE, U+0100->U+0101, U+0101, U+0102->U+0103, U+0103, U+0104->U+0105, U+0105, U+0106->U+0107, U+0107, U+0108->U+0109, U+0109, U+010A->U+010B, U+010B, U+010C->U+010D, U+010D, U+010E->U+010F, U+010F, U+0110->U+0111, U+0111, U+0112->U+0113, U+0113, U+0114->U+0115, U+0115, U+0116->U+0117, U+0117, U+0118->U+0119, U+0119, U+011A->U+011B, U+011B, U+011C->U+011D, U+011D, U+011E->U+011F, U+011F, U+0130->U+0131, U+0131, U+0132->U+0133, U+0133, U+0134->U+0135, U+0135, U+0136->U+0137, U+0137, U+0139->U+013A, U+013A, U+013B->U+013C, U+013C, U+013D->U+013E, U+013E, U+013F->U+0140, U+0140, U+0141->U+0142, U+0142, U+0143->U+0144, U+0144, U+0145->U+0146, U+0146, U+0147->U+0148, U+0148, U+014A->U+014B, U+014B, U+014C->U+014D, U+014D, U+014E->U+014F, U+014F, U+0150->U+0151, U+0151, U+0152->U+0153, U+0153, U+0154->U+0155, U+0155, U+0156->U+0157, U+0157, U+0158->U+0159, U+0159, U+015A->U+015B, U+015B, U+015C->U+015D, U+015D, U+015E->U+015F, U+015F, U+0160->U+0161, U+0161, U+0162->U+0163, U+0163, U+0164->U+0165, U+0165, U+0166->U+0167, U+0167, U+0168->U+0169, U+0169, U+016A->U+016B, U+016B, U+016C->U+016D, U+016D, U+016E->U+016F, U+016F, U+0170->U+0171, U+0171, U+0172->U+0173, U+0173, U+0174->U+0175, U+0175, U+0176->U+0177, U+0177, U+0178->U+00FF, U+00FF, U+0179->U+017A, U+017A, U+017B->U+017C, U+017C, U+017D->U+017E, U+017E, U+0410..U+042F->U+0430..U+044F, U+0430..U+044F, U+4E00..U+9FFF'),
				array('min_prefix_len',				'0'),
				array('min_infix_len',				'0'),
			),
			'index index_phpbb_' . $this->id . '_delta : index_phpbb_' . $this->id . '_main' => array(
				array('path',						$this->config['fulltext_sphinx_data_path'] . 'index_phpbb_' . $this->id . '_delta'),
				array('source',						'source_phpbb_' . $this->id . '_delta'),
			),
			'indexer' => array(
				array('mem_limit',					$this->config['fulltext_sphinx_indexer_mem_limit'] . 'M'),
			),
			'searchd' => array(
				array('compat_sphinxql_magics'	,	'0'),
				array('listen'	,					($this->config['fulltext_sphinx_host'] ? $this->config['fulltext_sphinx_host'] : 'localhost') . ':' . ($this->config['fulltext_sphinx_port'] ? $this->config['fulltext_sphinx_port'] : '9312')),
				array('log',						$this->config['fulltext_sphinx_data_path'] . 'log/searchd.log'),
				array('query_log',					$this->config['fulltext_sphinx_data_path'] . 'log/sphinx-query.log'),
				array('read_timeout',				'5'),
				array('max_children',				'30'),
				array('pid_file',					$this->config['fulltext_sphinx_data_path'] . 'searchd.pid'),
				array('max_matches',				(string) SPHINX_MAX_MATCHES),
				array('binlog_path',				$this->config['fulltext_sphinx_data_path']),
			),
		);

		$non_unique = array('sql_query_pre' => true, 'sql_attr_uint' => true, 'sql_attr_timestamp' => true, 'sql_attr_str2ordinal' => true, 'sql_attr_bool' => true);
		$delete = array('sql_group_column' => true, 'sql_date_column' => true, 'sql_str2ordinal_column' => true);
		foreach ($config_data as $section_name => $section_data)
		{
			$section = $config_object->get_section_by_name($section_name);
			if (!$section)
			{
				$section = $config_object->add_section($section_name);
			}

			foreach ($delete as $key => $void)
			{
				$section->delete_variables_by_name($key);
			}

			foreach ($non_unique as $key => $void)
			{
				$section->delete_variables_by_name($key);
			}

			foreach ($section_data as $entry)
			{
				$key = $entry[0];
				$value = $entry[1];

				if (!isset($non_unique[$key]))
				{
					$variable = $section->get_variable_by_name($key);
					if (!$variable)
					{
						$variable = $section->create_variable($key, $value);
					}
					else
					{
						$variable->set_value($value);
					}
				}
				else
				{
					$variable = $section->create_variable($key, $value);
				}
			}
		}
		$this->config_file_data = $config_object->get_data();

		return true;
	}

	/**
	* Splits keywords entered by a user into an array of words stored in $this->split_words
	* Stores the tidied search query in $this->search_query
	*
	* @param string $keywords Contains the keyword as entered by the user
	* @param string $terms is either 'all' or 'any'
	* @return false if no valid keywords were found and otherwise true
	*
	* @access public
	*/
	function split_keywords(&$keywords, $terms)
	{
		if ($terms == 'all')
		{
			$match		= array('#\sand\s#i', '#\sor\s#i', '#\snot\s#i', '#\+#', '#-#', '#\|#', '#@#');
			$replace	= array(' & ', ' | ', '  - ', ' +', ' -', ' |', '');

			$replacements = 0;
			$keywords = preg_replace($match, $replace, $keywords);
			$this->sphinx->SetMatchMode(SPH_MATCH_EXTENDED);
		}
		else
		{
			$this->sphinx->SetMatchMode(SPH_MATCH_ANY);
		}

		// Keep quotes and new lines
		$keywords = str_replace(array('&quot;', "\n"), array('"', ' '), trim($keywords));

		if (strlen($keywords) > 0)
		{
			$this->search_query = str_replace('"', '&quot;', $keywords);
			return true;
		}

		return false;
	}

	/**
	* Performs a search on keywords depending on display specific params. You have to run split_keywords() first.
	*
	* @param	string		$type				contains either posts or topics depending on what should be searched for
	* @param	string		$fields				contains either titleonly (topic titles should be searched), msgonly (only message bodies should be searched), firstpost (only subject and body of the first post should be searched) or all (all post bodies and subjects should be searched)
	* @param	string		$terms				is either 'all' (use query as entered, words without prefix should default to "have to be in field") or 'any' (ignore search query parts and just return all posts that contain any of the specified words)
	* @param	array		$sort_by_sql		contains SQL code for the ORDER BY part of a query
	* @param	string		$sort_key			is the key of $sort_by_sql for the selected sorting
	* @param	string		$sort_dir			is either a or d representing ASC and DESC
	* @param	string		$sort_days			specifies the maximum amount of days a post may be old
	* @param	array		$ex_fid_ary			specifies an array of forum ids which should not be searched
	* @param	array		$m_approve_fid_ary	specifies an array of forum ids in which the searcher is allowed to view unapproved posts
	* @param	int			$topic_id			is set to 0 or a topic id, if it is not 0 then only posts in this topic should be searched
	* @param	array		$author_ary			an array of author ids if the author should be ignored during the search the array is empty
	* @param	string		$author_name		specifies the author match, when ANONYMOUS is also a search-match
	* @param	array		&$id_ary			passed by reference, to be filled with ids for the page specified by $start and $per_page, should be ordered
	* @param	int			$start				indicates the first index of the page
	* @param	int			$per_page			number of ids each page is supposed to contain
	* @return	boolean|int						total number of results
	*
	* @access	public
	*/
	function keyword_search($type, $fields, $terms, $sort_by_sql, $sort_key, $sort_dir, $sort_days, $ex_fid_ary, $m_approve_fid_ary, $topic_id, $author_ary, $author_name, &$id_ary, $start, $per_page)
	{
		// No keywords? No posts.
		if (!strlen($this->search_query) && !sizeof($author_ary))
		{
			return false;
		}

		$id_ary = array();

		$join_topic = ($type != 'posts');

		// Sorting

		if ($type == 'topics')
		{
			switch ($sort_key)
			{
				case 'a':
					$this->sphinx->SetGroupBy('topic_id', SPH_GROUPBY_ATTR, 'poster_id ' . (($sort_dir == 'a') ? 'ASC' : 'DESC'));
				break;

				case 'f':
					$this->sphinx->SetGroupBy('topic_id', SPH_GROUPBY_ATTR, 'forum_id ' . (($sort_dir == 'a') ? 'ASC' : 'DESC'));
				break;

				case 'i':

				case 's':
					$this->sphinx->SetGroupBy('topic_id', SPH_GROUPBY_ATTR, 'post_subject ' . (($sort_dir == 'a') ? 'ASC' : 'DESC'));
				break;

				case 't':

				default:
					$this->sphinx->SetGroupBy('topic_id', SPH_GROUPBY_ATTR, 'topic_last_post_time ' . (($sort_dir == 'a') ? 'ASC' : 'DESC'));
				break;
			}
		}
		else
		{
			switch ($sort_key)
			{
				case 'a':
					$this->sphinx->SetSortMode(($sort_dir == 'a') ? SPH_SORT_ATTR_ASC : SPH_SORT_ATTR_DESC, 'poster_id');
				break;

				case 'f':
					$this->sphinx->SetSortMode(($sort_dir == 'a') ? SPH_SORT_ATTR_ASC : SPH_SORT_ATTR_DESC, 'forum_id');
				break;

				case 'i':

				case 's':
					$this->sphinx->SetSortMode(($sort_dir == 'a') ? SPH_SORT_ATTR_ASC : SPH_SORT_ATTR_DESC, 'post_subject');
				break;

				case 't':

				default:
					$this->sphinx->SetSortMode(($sort_dir == 'a') ? SPH_SORT_ATTR_ASC : SPH_SORT_ATTR_DESC, 'post_time');
				break;
			}
		}

		// Most narrow filters first
		if ($topic_id)
		{
			$this->sphinx->SetFilter('topic_id', array($topic_id));
		}

		$search_query_prefix = '';

		switch ($fields)
		{
			case 'titleonly':
				// Only search the title
				if ($terms == 'all')
				{
					$search_query_prefix = '@title ';
				}
				// Weight for the title
				$this->sphinx->SetFieldWeights(array("title" => 5, "data" => 1));
				// 1 is first_post, 0 is not first post
				$this->sphinx->SetFilter('topic_first_post', array(1));
			break;

			case 'msgonly':
				// Only search the body
				if ($terms == 'all')
				{
					$search_query_prefix = '@data ';
				}
				// Weight for the body
				$this->sphinx->SetFieldWeights(array("title" => 1, "data" => 5));
			break;

			case 'firstpost':
				// More relative weight for the title, also search the body
				$this->sphinx->SetFieldWeights(array("title" => 5, "data" => 1));
				// 1 is first_post, 0 is not first post
				$this->sphinx->SetFilter('topic_first_post', array(1));
			break;

			default:
				// More relative weight for the title, also search the body
				$this->sphinx->SetFieldWeights(array("title" => 5, "data" => 1));
			break;
		}

		if (sizeof($author_ary))
		{
			$this->sphinx->SetFilter('poster_id', $author_ary);
		}

		if (sizeof($ex_fid_ary))
		{
			// All forums that a user is allowed to access
			$fid_ary = array_unique(array_intersect(array_keys($this->auth->acl_getf('f_read', true)), array_keys($this->auth->acl_getf('f_search', true))));
			// All forums that the user wants to and can search in
			$search_forums = array_diff($fid_ary, $ex_fid_ary);

			if (sizeof($search_forums))
			{
				$this->sphinx->SetFilter('forum_id', $search_forums);
			}
		}

		$this->sphinx->SetFilter('deleted', array(0));

		$this->sphinx->SetLimits($start, (int) $per_page, SPHINX_MAX_MATCHES);
		$result = $this->sphinx->Query($search_query_prefix . str_replace('&quot;', '"', $this->search_query), $this->indexes);

		// Could be connection to localhost:9312 failed (errno=111,
		// msg=Connection refused) during rotate, retry if so
		$retries = SPHINX_CONNECT_RETRIES;
		while (!$result && (strpos($this->sphinx->_error, "errno=111,") !== false) && $retries--)
		{
			usleep(SPHINX_CONNECT_WAIT_TIME);
			$result = $this->sphinx->Query($search_query_prefix . str_replace('&quot;', '"', $this->search_query), $this->indexes);
		}

		$id_ary = array();
		if (isset($result['matches']))
		{
			if ($type == 'posts')
			{
				$id_ary = array_keys($result['matches']);
			}
			else
			{
				foreach ($result['matches'] as $key => $value)
				{
					$id_ary[] = $value['attrs']['topic_id'];
				}
			}
		}
		else
		{
			return false;
		}

		$result_count = $result['total_found'];

		$id_ary = array_slice($id_ary, 0, (int) $per_page);

		return $result_count;
	}

	/**
	* Performs a search on an author's posts without caring about message contents. Depends on display specific params
	*
	* @param	string		$type				contains either posts or topics depending on what should be searched for
	* @param	boolean		$firstpost_only		if true, only topic starting posts will be considered
	* @param	array		$sort_by_sql		contains SQL code for the ORDER BY part of a query
	* @param	string		$sort_key			is the key of $sort_by_sql for the selected sorting
	* @param	string		$sort_dir			is either a or d representing ASC and DESC
	* @param	string		$sort_days			specifies the maximum amount of days a post may be old
	* @param	array		$ex_fid_ary			specifies an array of forum ids which should not be searched
	* @param	array		$m_approve_fid_ary	specifies an array of forum ids in which the searcher is allowed to view unapproved posts
	* @param	int			$topic_id			is set to 0 or a topic id, if it is not 0 then only posts in this topic should be searched
	* @param	array		$author_ary			an array of author ids
	* @param	string		$author_name		specifies the author match, when ANONYMOUS is also a search-match
	* @param	array		&$id_ary			passed by reference, to be filled with ids for the page specified by $start and $per_page, should be ordered
	* @param	int			$start				indicates the first index of the page
	* @param	int			$per_page			number of ids each page is supposed to contain
	* @return	boolean|int						total number of results
	*
	* @access	public
	*/
	function author_search($type, $firstpost_only, $sort_by_sql, $sort_key, $sort_dir, $sort_days, $ex_fid_ary, $m_approve_fid_ary, $topic_id, $author_ary, $author_name, &$id_ary, $start, $per_page)
	{
		$this->search_query = '';

		$this->sphinx->SetMatchMode(SPH_MATCH_FULLSCAN);
		$fields = ($firstpost_only) ? 'firstpost' : 'all';
		$terms = 'all';
		return $this->keyword_search($type, $fields, $terms, $sort_by_sql, $sort_key, $sort_dir, $sort_days, $ex_fid_ary, $m_approve_fid_ary, $topic_id, $author_ary, $author_name, $id_ary, $start, $per_page);
	}

	/**
	 * Updates wordlist and wordmatch tables when a message is posted or changed
	 *
	 * @param	string	$mode	Contains the post mode: edit, post, reply, quote
	 * @param	int	$post_id	The id of the post which is modified/created
	 * @param	string	&$message	New or updated post content
	 * @param	string	&$subject	New or updated post subject
	 * @param	int	$poster_id	Post author's user id
	 * @param	int	$forum_id	The id of the forum in which the post is located
	 *
	 * @access public
	 */
	function index($mode, $post_id, &$message, &$subject, $poster_id, $forum_id)
	{
		if ($mode == 'edit')
		{
			$this->sphinx->UpdateAttributes($this->indexes, array('forum_id', 'poster_id'), array((int)$post_id => array((int)$forum_id, (int)$poster_id)));
		}
		else if ($mode != 'post' && $post_id)
		{
			// Update topic_last_post_time for full topic
			$sql_array = array(
				'SELECT'	=> 'p1.post_id',
				'FROM'		=> array(
					POSTS_TABLE	=> 'p1',
				),
				'LEFT_JOIN'	=> array(array(
					'FROM'	=> array(
						POSTS_TABLE	=> 'p2'
					),
					'ON'	=> 'p1.topic_id = p2.topic_id',
				)),
			);

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			$post_updates = array();
			$post_time = time();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$post_updates[(int)$row['post_id']] = array($post_time);
			}
			$this->db->sql_freeresult($result);

			if (sizeof($post_updates))
			{
				$this->sphinx->UpdateAttributes($this->indexes, array('topic_last_post_time'), $post_updates);
			}
		}
	}

	/**
	* Delete a post from the index after it was deleted
	*
	* @access public
	*/
	function index_remove($post_ids, $author_ids, $forum_ids)
	{
		$values = array();
		foreach ($post_ids as $post_id)
		{
			$values[$post_id] = array(1);
		}

		$this->sphinx->UpdateAttributes($this->indexes, array('deleted'), $values);
	}

	/**
	* Nothing needs to be destroyed
	*
	* @access public
	*/
	function tidy($create = false)
	{
		set_config('search_last_gc', time(), true);
	}

	/**
	* Create sphinx table
	*
	* @return string|bool error string is returned incase of errors otherwise false
	*
	* @access public
	*/
	function create_index($acp_module, $u_action)
	{
		if (!$this->index_created())
		{
			$table_data = array(
				'COLUMNS'	=> array(
					'counter_id'	=> array('UINT', 0),
					'max_doc_id'	=> array('UINT', 0),
				),
				'PRIMARY_KEY'	=> 'counter_id',
			);
			$this->db_tools->sql_create_table(SPHINX_TABLE, $table_data);

			$sql = 'TRUNCATE TABLE ' . SPHINX_TABLE;
			$this->db->sql_query($sql);

			$data = array(
				'counter_id'	=> '1',
				'max_doc_id'	=> '0',
			);
			$sql = 'INSERT INTO ' . SPHINX_TABLE . ' ' . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
		}

		return false;
	}

	/**
	* Drop sphinx table
	*
	* @return string|bool error string is returned incase of errors otherwise false
	*
	* @access public
	*/
	function delete_index($acp_module, $u_action)
	{
		if (!$this->index_created())
		{
			return false;
		}

		$this->db_tools->sql_table_drop(SPHINX_TABLE);

		return false;
	}

	/**
	* Returns true if the sphinx table was created
	*
	* @return bool true if sphinx table was created
	*
	* @access public
	*/
	function index_created($allow_new_files = true)
	{
		$created = false;

		if ($this->db_tools->sql_table_exists(SPHINX_TABLE))
		{
			$created = true;
		}

		return $created;
	}

	/**
	* Returns an associative array containing information about the indexes
	*
	* @return string|bool Language string of error false otherwise
	*
	* @access public
	*/
	function index_stats()
	{
		if (empty($this->stats))
		{
			$this->get_stats();
		}

		return array(
			$this->user->lang['FULLTEXT_SPHINX_MAIN_POSTS']			=> ($this->index_created()) ? $this->stats['main_posts'] : 0,
			$this->user->lang['FULLTEXT_SPHINX_DELTA_POSTS']			=> ($this->index_created()) ? $this->stats['total_posts'] - $this->stats['main_posts'] : 0,
			$this->user->lang['FULLTEXT_MYSQL_TOTAL_POSTS']			=> ($this->index_created()) ? $this->stats['total_posts'] : 0,
		);
	}

	/**
	* Collects stats that can be displayed on the index maintenance page
	*
	* @access private
	*/
	function get_stats()
	{
		if ($this->index_created())
		{
			$sql = 'SELECT COUNT(post_id) as total_posts
				FROM ' . POSTS_TABLE;
			$result = $this->db->sql_query($sql);
			$this->stats['total_posts'] = (int) $this->db->sql_fetchfield('total_posts');
			$this->db->sql_freeresult($result);

			$sql = 'SELECT COUNT(p.post_id) as main_posts
				FROM ' . POSTS_TABLE . ' p, ' . SPHINX_TABLE . ' m
				WHERE p.post_id <= m.max_doc_id
					AND m.counter_id = 1';
			$result = $this->db->sql_query($sql);
			$this->stats['main_posts'] = (int) $this->db->sql_fetchfield('main_posts');
			$this->db->sql_freeresult($result);
		}
	}

	/**
	* Returns a list of options for the ACP to display
	*
	* @return associative array containing template and config variables
	*
	* @access public
	*/
	function acp()
	{
		$config_vars = array(
			'fulltext_sphinx_data_path' => 'string',
			'fulltext_sphinx_host' => 'string',
			'fulltext_sphinx_port' => 'string',
			'fulltext_sphinx_indexer_mem_limit' => 'int',
		);

		$tpl = '
		<span class="error">' . $this->user->lang['FULLTEXT_SPHINX_CONFIGURE']. '</span>
		<dl>
			<dt><label for="fulltext_sphinx_data_path">' . $this->user->lang['FULLTEXT_SPHINX_DATA_PATH'] . ':</label><br /><span>' . $this->user->lang['FULLTEXT_SPHINX_DATA_PATH_EXPLAIN'] . '</span></dt>
			<dd><input id="fulltext_sphinx_data_path" type="text" size="40" maxlength="255" name="config[fulltext_sphinx_data_path]" value="' . $this->config['fulltext_sphinx_data_path'] . '" /></dd>
		</dl>
		<dl>
			<dt><label for="fulltext_sphinx_host">' . $this->user->lang['FULLTEXT_SPHINX_HOST'] . ':</label><br /><span>' . $this->user->lang['FULLTEXT_SPHINX_HOST_EXPLAIN'] . '</span></dt>
			<dd><input id="fulltext_sphinx_host" type="text" size="40" maxlength="255" name="config[fulltext_sphinx_host]" value="' . $this->config['fulltext_sphinx_host'] . '" /></dd>
		</dl>
		<dl>
			<dt><label for="fulltext_sphinx_port">' . $this->user->lang['FULLTEXT_SPHINX_PORT'] . ':</label><br /><span>' . $this->user->lang['FULLTEXT_SPHINX_PORT_EXPLAIN'] . '</span></dt>
			<dd><input id="fulltext_sphinx_port" type="text" size="4" maxlength="10" name="config[fulltext_sphinx_port]" value="' . $this->config['fulltext_sphinx_port'] . '" /></dd>
		</dl>
		<dl>
			<dt><label for="fulltext_sphinx_indexer_mem_limit">' . $this->user->lang['FULLTEXT_SPHINX_INDEXER_MEM_LIMIT'] . ':</label><br /><span>' . $this->user->lang['FULLTEXT_SPHINX_INDEXER_MEM_LIMIT_EXPLAIN'] . '</span></dt>
			<dd><input id="fulltext_sphinx_indexer_mem_limit" type="text" size="4" maxlength="10" name="config[fulltext_sphinx_indexer_mem_limit]" value="' . $this->config['fulltext_sphinx_indexer_mem_limit'] . '" />' . $this->user->lang['MIB'] . '</dd>
		</dl>
		<dl>
			<dt><label for="fulltext_sphinx_config_file">' . $this->user->lang['FULLTEXT_SPHINX_CONFIG_FILE'] . ':</label><br /><span>' . $this->user->lang['FULLTEXT_SPHINX_CONFIG_FILE_EXPLAIN'] . '</dt>
			<dd>' . (($this->config_generate()) ? '<textarea readonly="readonly" rows="6">' . $this->config_file_data . '</textarea>' : $this->config_file_data) . '</dd>
		<dl>
		';

		// These are fields required in the config table
		return array(
			'tpl'		=> $tpl,
			'config'	=> $config_vars
		);
	}
}
