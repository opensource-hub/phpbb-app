/****** Object:  Table [devhttp].[phpbb_config]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_config]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_config]

GO



/****** Object:  Table [devhttp].[phpbb_disallow]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_disallow]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_disallow]

GO



/****** Object:  Table [devhttp].[phpbb_forum_prune]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_forum_prune]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_forum_prune]

GO



/****** Object:  Table [devhttp].[phpbb_forums]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_forums]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_forums]

GO



/****** Object:  Table [devhttp].[phpbb_groups]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_groups]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_groups]

GO



/****** Object:  Table [devhttp].[phpbb_posts]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_posts]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_posts]

GO



/****** Object:  Table [devhttp].[phpbb_posts_text]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_posts_text]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_posts_text]

GO



/****** Object:  Table [devhttp].[phpbb_privmsgs]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_privmsgs]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_privmsgs]

GO



/****** Object:  Table [devhttp].[phpbb_privmsgs_text]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_privmsgs_text]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_privmsgs_text]

GO



/****** Object:  Table [devhttp].[phpbb_ranks]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_ranks]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_ranks]

GO



/****** Object:  Table [devhttp].[phpbb_session]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_session]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_session]

GO



/****** Object:  Table [devhttp].[phpbb_smilies]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_smilies]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_smilies]

GO



/****** Object:  Table [devhttp].[phpbb_themes]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_themes]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_themes]

GO



/****** Object:  Table [devhttp].[phpbb_themes_name]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_themes_name]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_themes_name]

GO



/****** Object:  Table [devhttp].[phpbb_topics]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_topics]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_topics]

GO



/****** Object:  Table [devhttp].[phpbb_user_group]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_user_group]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_user_group]

GO



/****** Object:  Table [devhttp].[phpbb_users]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_users]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_users]

GO



/****** Object:  Table [devhttp].[phpbb_users_extra]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_users_extra]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_users_extra]

GO



/****** Object:  Table [devhttp].[phpbb_words]    Script Date: 22/07/2001 17:16:47 ******/

if exists (select * from dbo.sysobjects where id = object_id(N'[devhttp].[phpbb_words]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)

drop table [devhttp].[phpbb_words]

GO



/****** Object:  Table [devhttp].[phpbb_config]    Script Date: 22/07/2001 17:16:51 ******/

CREATE TABLE [devhttp].[phpbb_config] (

	[config_id] [int] NULL ,

	[board_disable] [tinyint] NULL , 

	[sitename] [char] (100) NULL ,

	[cookie_name] [char] (20) NULL ,

	[cookie_path] [char] (25) NULL ,

	[cookie_domain] [char] (50) NULL ,

	[cookie_secure] [tinyint] NULL ,

	[session_length] [int] NULL ,

	[gzip_compress] [smallint] NOT NULL ,

	[prune_enable] [smallint] NOT NULL ,

	[allow_html] [smallint] NULL ,

	[allow_bbcode] [smallint] NULL ,

	[allow_smilies] [smallint] NOT NULL ,

	[allow_sig] [smallint] NULL ,

	[allow_namechange] [smallint] NULL ,

	[allow_privmsg] [smallint] NOT NULL ,

	[allow_avatar_remote] [smallint] NOT NULL ,

	[allow_avatar_local] [smallint] NOT NULL ,

	[allow_avatar_upload] [smallint] NOT NULL ,

	[allow_theme_create] [int] NULL ,

	[override_themes] [smallint] NULL ,

	[flood_interval] [int] NOT NULL ,

	[post_mod_time] [int] NULL ,

	[posts_per_page] [int] NULL ,

	[hot_threshold] [int] NULL ,

	[topics_per_page] [int] NULL ,

	[email_sig] [char] (255) NULL ,

	[email_from] [char] (100) NULL ,

	[smtp_delivery] [tinyint] NULL ,

	[smtp_host] [char] (50) NULL ,

	[default_theme] [int] NOT NULL ,

	[default_lang] [char] (255) NULL ,

	[default_dateformat] [char] (14) NOT NULL ,

	[system_timezone] [int] NOT NULL ,

	[sys_template] [char] (100) NOT NULL ,

	[avatar_filesize] [int] NULL ,

	[avatar_path] [char] (255) NULL ,

	[avatar_max_width] [smallint] NULL ,

	[avatar_max_height] [smallint] NULL

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_disallow]    Script Date: 22/07/2001 17:16:52 ******/

CREATE TABLE [devhttp].[phpbb_disallow] (

	[disallow_id] [int] NULL ,

	[disallow_username] [varchar] (50) NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_forum_prune]    Script Date: 22/07/2001 17:16:52 ******/

CREATE TABLE [devhttp].[phpbb_forum_prune] (

	[prune_id] [int] NULL ,

	[forum_id] [int] NOT NULL ,

	[prune_days] [int] NOT NULL ,

	[prune_freq] [int] NOT NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_forums]    Script Date: 22/07/2001 17:16:52 ******/

CREATE TABLE [devhttp].[phpbb_forums] (

	[forum_id] [int] NOT NULL ,

	[forum_name] [varchar] (150) NOT NULL ,

	[forum_desc] [text] NOT NULL ,

	[cat_id] [int] NOT NULL ,

	[forum_order] [int] NOT NULL ,

	[forum_posts] [int] NULL ,

	[forum_topics] [int] NULL ,

	[forum_last_post_id] [int] NULL ,

	[prune_next] [int] NULL ,

	[prune_enable] [smallint] NOT NULL ,

	[auth_view] [smallint] NOT NULL ,

	[auth_read] [smallint] NOT NULL ,

	[auth_post] [smallint] NOT NULL ,

	[auth_sticky] [smallint] NOT NULL ,

	[auth_announce] [smallint] NOT NULL ,

	[auth_reply] [smallint] NOT NULL ,

	[auth_edit] [smallint] NOT NULL ,

	[auth_delete] [smallint] NOT NULL ,

	[auth_votecreate] [smallint] NOT NULL ,

	[auth_vote] [smallint] NOT NULL ,

	[auth_attachments] [smallint] NOT NULL 

) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_groups]    Script Date: 22/07/2001 17:16:53 ******/

CREATE TABLE [devhttp].[phpbb_groups] (

	[group_id] [int] IDENTITY (1, 1) NOT NULL ,

	[group_type] [smallint] NOT NULL ,

	[group_name] [varchar] (100) NOT NULL ,

	[group_description] [varchar] (255) NOT NULL ,

	[group_single_user] [smallint] NOT NULL ,

	[group_moderator] [int] NOT NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_posts]    Script Date: 22/07/2001 17:16:53 ******/

CREATE TABLE [devhttp].[phpbb_posts] (

	[post_id] [int] IDENTITY (1, 1) NOT NULL ,

	[topic_id] [int] NOT NULL ,

	[forum_id] [int] NOT NULL ,

	[poster_id] [int] NOT NULL ,

	[post_time] [int] NOT NULL ,

	[post_username] [varchar] (50) NULL ,

	[poster_ip] [varchar] (8) NOT NULL ,

	[bbcode_uid] [varchar] (10) NOT NULL ,

	[post_edit_time] [int] NULL ,

	[post_edit_count] [smallint] NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_posts_text]    Script Date: 22/07/2001 17:16:54 ******/

CREATE TABLE [devhttp].[phpbb_posts_text] (

	[post_id] [int] NOT NULL ,

	[post_subject] [varchar] (255) NULL ,

	[post_text] [text] NULL 

) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_privmsgs]    Script Date: 22/07/2001 17:16:54 ******/

CREATE TABLE [devhttp].[phpbb_privmsgs] (

	[privmsgs_id] [int] IDENTITY (1, 1) NOT NULL ,

	[privmsgs_type] [smallint] NOT NULL ,

	[privmsgs_subject] [varchar] (255) NOT NULL ,

	[privmsgs_from_userid] [int] NOT NULL ,

	[privmsgs_to_userid] [int] NOT NULL ,

	[privmsgs_date] [int] NOT NULL ,

	[privmsgs_ip] [varchar] (8) NOT NULL ,

	[privmsgs_bbcode_uid] [varchar] (10) NOT NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_privmsgs_text]    Script Date: 22/07/2001 17:16:54 ******/

CREATE TABLE [devhttp].[phpbb_privmsgs_text] (

	[privmsgs_text_id] [int] NOT NULL ,

	[privmsgs_text] [text] NULL 

) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_ranks]    Script Date: 22/07/2001 17:16:55 ******/

CREATE TABLE [devhttp].[phpbb_ranks] (

	[rank_id] [int] NULL ,

	[rank_title] [varchar] (50) NOT NULL ,

	[rank_min] [int] NOT NULL ,

	[rank_max] [int] NOT NULL ,

	[rank_special] [int] NULL ,

	[rank_image] [varchar] (255) NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_session]    Script Date: 22/07/2001 17:16:55 ******/

CREATE TABLE [devhttp].[phpbb_session] (

	[session_id] [char] (32) NOT NULL ,

	[session_user_id] [int] NOT NULL ,

	[session_start] [int] NOT NULL ,

	[session_time] [int] NOT NULL ,

	[session_last_visit] [int] NOT NULL ,

	[session_ip] [char] (8) NOT NULL ,

	[session_page] [int] NOT NULL ,

	[session_logged_in] [smallint] NOT NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_smilies]    Script Date: 22/07/2001 17:16:55 ******/

CREATE TABLE [devhttp].[phpbb_smilies] (

	[smilies_id] [int] NULL ,

	[code] [varchar] (50) NULL ,

	[smile_url] [varchar] (100) NULL ,

	[emoticon] [varchar] (75) NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_themes]    Script Date: 22/07/2001 17:16:55 ******/

CREATE TABLE [devhttp].[phpbb_themes] (

	[themes_id] [int] NULL ,

	[themes_name] [varchar] (30) NULL ,

	[head_stylesheet] [varchar] (100) NULL ,

	[body_background] [varchar] (100) NULL ,

	[body_bgcolor] [varchar] (6) NULL ,

	[body_text] [varchar] (6) NULL ,

	[body_link] [varchar] (6) NULL ,

	[body_vlink] [varchar] (6) NULL ,

	[body_alink] [varchar] (6) NULL ,

	[body_hlink] [varchar] (6) NULL ,

	[tr_color1] [varchar] (6) NULL ,

	[tr_color2] [varchar] (6) NULL ,

	[tr_color3] [varchar] (6) NULL ,

	[th_color1] [varchar] (6) NULL ,

	[th_color2] [varchar] (6) NULL ,

	[th_color3] [varchar] (6) NULL ,

	[td_color1] [varchar] (6) NULL ,

	[td_color2] [varchar] (6) NULL ,

	[td_color3] [varchar] (6) NULL ,

	[fontface1] [varchar] (40) NULL ,

	[fontface2] [varchar] (40) NULL ,

	[fontface3] [varchar] (40) NULL ,

	[fontsize1] [smallint] NULL ,

	[fontsize2] [smallint] NULL ,

	[fontsize3] [smallint] NULL ,

	[fontcolor1] [varchar] (6) NULL ,

	[fontcolor2] [varchar] (6) NULL ,

	[fontcolor3] [varchar] (6) NULL ,

	[img1] [varchar] (100) NULL ,

	[img2] [varchar] (100) NULL ,

	[img3] [varchar] (100) NULL ,

	[img4] [varchar] (100) NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_themes_name]    Script Date: 22/07/2001 17:16:56 ******/

CREATE TABLE [devhttp].[phpbb_themes_name] (

	[themes_id] [int] NOT NULL ,

	[tr_color1_name] [varchar] (25) NULL ,

	[tr_color2_name] [varchar] (25) NULL ,

	[tr_color3_name] [varchar] (25) NULL ,

	[th_color1_name] [varchar] (25) NULL ,

	[th_color2_name] [varchar] (25) NULL ,

	[th_color3_name] [varchar] (25) NULL ,

	[td_color1_name] [varchar] (25) NULL ,

	[td_color2_name] [varchar] (25) NULL ,

	[td_color3_name] [varchar] (25) NULL ,

	[fontface1_name] [varchar] (25) NULL ,

	[fontface2_name] [varchar] (25) NULL ,

	[fontface3_name] [varchar] (25) NULL ,

	[fontsize1_name] [varchar] (25) NULL ,

	[fontsize2_name] [varchar] (25) NULL ,

	[fontsize3_name] [varchar] (25) NULL ,

	[fontcolor1_name] [varchar] (25) NULL ,

	[fontcolor2_name] [varchar] (25) NULL ,

	[fontcolor3_name] [varchar] (25) NULL ,

	[img1_name] [varchar] (25) NULL ,

	[img2_name] [varchar] (25) NULL ,

	[img3_name] [varchar] (25) NULL ,

	[img4_name] [varchar] (25) NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_topics]    Script Date: 22/07/2001 17:16:56 ******/

CREATE TABLE [devhttp].[phpbb_topics] (

	[topic_id] [int] IDENTITY (1, 1) NOT NULL ,

	[topic_title] [varchar] (100) NOT NULL ,

	[topic_poster] [int] NOT NULL ,

	[topic_time] [int] NOT NULL ,

	[topic_views] [int] NULL ,

	[topic_replies] [int] NULL ,

	[forum_id] [int] NOT NULL ,

	[topic_status] [smallint] NULL ,

	[topic_type] [smallint] NULL ,

	[topic_notify] [smallint] NULL ,

	[topic_last_post_id] [int] NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_user_group]    Script Date: 22/07/2001 17:16:56 ******/

CREATE TABLE [devhttp].[phpbb_user_group] (

	[group_id] [int] NOT NULL ,

	[user_id] [int] NOT NULL ,

	[user_pending] [smallint] NOT NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_users]    Script Date: 22/07/2001 17:16:57 ******/

CREATE TABLE [devhttp].[phpbb_users] (

	[user_id] [int] IDENTITY (1, 1) NOT NULL ,

	[username] [varchar] (40) NOT NULL ,

	[user_level] [smallint] NOT NULL ,

	[user_regdate] [int] NOT NULL ,

	[user_password] [varchar] (32) NOT NULL ,

	[user_autologin_key] [varchar] (32) NULL ,

	[user_email] [varchar] (255) NULL ,

	[user_icq] [varchar] (15) NULL ,

	[user_website] [varchar] (100) NULL ,

	[user_occ] [varchar] (100) NULL ,

	[user_from] [varchar] (100) NULL ,

	[user_interests] [varchar] (255) NULL ,

	[user_sig] [varchar] (255) NULL ,

	[user_viewemail] [smallint] NULL ,

	[user_theme] [int] NULL ,

	[user_aim] [varchar] (255) NULL ,

	[user_yim] [varchar] (255) NULL ,

	[user_msnm] [varchar] (255) NULL ,

	[user_posts] [int] NULL ,

	[user_attachsig] [smallint] NULL ,

	[user_allowsmile] [smallint] NULL ,

	[user_allowhtml] [smallint] NULL ,

	[user_allowbbcode] [smallint] NULL ,

	[user_allow_pm] [smallint] NOT NULL ,

	[user_allow_viewonline] [smallint] NOT NULL ,

	[user_notify_pm] [smallint] NOT NULL ,

	[user_rank] [int] NULL ,

	[user_avatar] [varchar] (100) NULL ,

	[user_lang] [varchar] (255) NULL ,

	[user_timezone] [float] NULL ,

	[user_dateformat] [varchar] (14) NULL ,

	[user_actkey] [varchar] (32) NULL ,

	[user_newpasswd] [varchar] (32) NULL ,

	[user_notify] [smallint] NULL ,

	[user_active] [smallint] NULL ,

	[user_template] [varchar] (50) NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_users_extra]    Script Date: 22/07/2001 17:16:57 ******/

CREATE TABLE [devhttp].[phpbb_users_extra] (

	[user_id] [int] NOT NULL ,

	[user_regdate] [int] NOT NULL ,

	[user_rank] [int] NULL ,

	[user_avatar] [varchar] (100) NULL ,

	[user_email] [varchar] (255) NULL ,

	[user_icq] [varchar] (15) NULL ,

	[user_website] [varchar] (100) NULL ,

	[user_occ] [varchar] (100) NULL ,

	[user_from] [varchar] (100) NULL ,

	[user_interests] [varchar] (255) NULL ,

	[user_sig] [varchar] (255) NULL ,

	[user_aim] [varchar] (255) NULL ,

	[user_yim] [varchar] (255) NULL ,

	[user_msnm] [varchar] (255) NULL ,

	[user_posts] [int] NULL ,

	[user_actkey] [varchar] (32) NULL ,

	[user_newpasswd] [varchar] (32) NULL ,

	[user_notify] [smallint] NULL 

) ON [PRIMARY]

GO



/****** Object:  Table [devhttp].[phpbb_words]    Script Date: 22/07/2001 17:16:57 ******/

CREATE TABLE [devhttp].[phpbb_words] (

	[word_id] [int] NULL ,

	[word] [varchar] (100) NOT NULL ,

	[replacement] [varchar] (100) NOT NULL 

) ON [PRIMARY]

GO



ALTER TABLE [devhttp].[phpbb_forums] WITH NOCHECK ADD 

	CONSTRAINT [PK_phpbb_forums] PRIMARY KEY  CLUSTERED 

	(

		[forum_id],

		[cat_id],

		[forum_order]

	)  ON [PRIMARY] 

GO



ALTER TABLE [devhttp].[phpbb_groups] WITH NOCHECK ADD 

	CONSTRAINT [PK_phpbb_groups] PRIMARY KEY  CLUSTERED 

	(

		[group_id],

		[group_single_user]

	)  ON [PRIMARY] 

GO



ALTER TABLE [devhttp].[phpbb_privmsgs] WITH NOCHECK ADD 

	CONSTRAINT [PK_phpbb_privmsgs] PRIMARY KEY  CLUSTERED 

	(

		[privmsgs_id]

	)  ON [PRIMARY] 

GO



ALTER TABLE [devhttp].[phpbb_session] WITH NOCHECK ADD 

	CONSTRAINT [PK_phpbb_session] PRIMARY KEY  CLUSTERED 

	(

		[session_id],

		[session_user_id],

		[session_ip]

	)  ON [PRIMARY] 

GO



ALTER TABLE [devhttp].[phpbb_topics] WITH NOCHECK ADD 

	CONSTRAINT [PK_phpbb_topics] PRIMARY KEY  CLUSTERED 

	(

		[topic_id],

		[topic_poster],

		[forum_id]

	)  ON [PRIMARY] 

GO



ALTER TABLE [devhttp].[phpbb_users] WITH NOCHECK ADD 

	CONSTRAINT [PK_phpbb_users] PRIMARY KEY  CLUSTERED 

	(

		[user_id]

	)  ON [PRIMARY] 

GO



 CREATE  UNIQUE  CLUSTERED  INDEX [IX_phpbb_posts] ON [devhttp].[phpbb_posts]([post_id]) ON [PRIMARY]

GO



ALTER TABLE [devhttp].[phpbb_config] WITH NOCHECK ADD 

	CONSTRAINT [DF_phpbb_config_smtp_delivery] DEFAULT (1) FOR [smtp_delivery],

	CONSTRAINT [DF_phpbb_config_gzip_compress] DEFAULT (0) FOR [gzip_compress],

	CONSTRAINT [DF_phpbb_config_allow_avatar_remote] DEFAULT (0) FOR [allow_avatar_remote]

GO



ALTER TABLE [devhttp].[phpbb_groups] WITH NOCHECK ADD 

	CONSTRAINT [DF_phpbb_groups_group_type] DEFAULT (1) FOR [group_type]

GO



ALTER TABLE [devhttp].[phpbb_posts] WITH NOCHECK ADD 

	CONSTRAINT [DF_phpbb_posts_post_edit_time] DEFAULT (0) FOR [post_edit_time],

	CONSTRAINT [DF_phpbb_posts_post_edit_count] DEFAULT (0) FOR [post_edit_count],

	CONSTRAINT [PK_phpbb_posts] PRIMARY KEY  NONCLUSTERED 

	(

		[post_id]

	)  ON [PRIMARY] 

GO



ALTER TABLE [devhttp].[phpbb_topics] WITH NOCHECK ADD 

	CONSTRAINT [DF_phpbb_topics_topic_views] DEFAULT (0) FOR [topic_views],

	CONSTRAINT [DF_phpbb_topics_topic_replies] DEFAULT (0) FOR [topic_replies]

GO



ALTER TABLE [devhttp].[phpbb_users] WITH NOCHECK ADD 

	CONSTRAINT [DF_phpbb_users_user_allow_viewonline] DEFAULT (1) FOR [user_allow_viewonline]

GO



 CREATE  INDEX [IX_phpbb_user_group] ON [devhttp].[phpbb_user_group]([group_id], [user_id]) ON [PRIMARY]

GO



