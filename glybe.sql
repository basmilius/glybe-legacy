SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `forum_categories` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `caption` varchar(500) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `min_permission` varchar(50) NOT NULL DEFAULT 'login',
  `min_post_permissions` varchar(50) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `forum_foras` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `category_id` int(25) NOT NULL DEFAULT '1',
  `caption` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `min_permission` varchar(50) NOT NULL,
  `min_post_permissions` varchar(50) NOT NULL DEFAULT '1',
  `permission_for_post` varchar(100) NOT NULL DEFAULT 'see_forum',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `forum_id` int(25) NOT NULL DEFAULT '0',
  `topic_id` int(25) NOT NULL DEFAULT '0',
  `state` enum('normal','deleted') NOT NULL DEFAULT 'normal',
  `message` text NOT NULL,
  `post_timestamp` int(25) NOT NULL,
  `last_edit` varchar(50) NOT NULL,
  `last_edit_by` int(10) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `first_post` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `forum_posts_old` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL,
  `forum_id` int(25) NOT NULL,
  `topic_id` int(25) NOT NULL,
  `state` enum('normal','deleted') NOT NULL,
  `message` text NOT NULL,
  `post_timestamp` int(25) NOT NULL,
  `post_id` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `forum_readed` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `forum_id` int(25) NOT NULL DEFAULT '0',
  `topic_id` int(25) NOT NULL DEFAULT '0',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `post_id` int(25) NOT NULL DEFAULT '0',
  `timestamp` int(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `forum_reports` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_from` int(10) NOT NULL,
  `post_id` int(10) NOT NULL,
  `reason` text NOT NULL,
  `date` varchar(500) NOT NULL,
  `behandeld` int(2) NOT NULL DEFAULT '0',
  `behandeld_door` int(10) NOT NULL,
  `behandeld_date` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `forum_id` int(25) NOT NULL DEFAULT '0',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `caption` varchar(500) NOT NULL,
  `state` enum('open','closed','deleted') NOT NULL DEFAULT 'open',
  `sticky` int(2) NOT NULL DEFAULT '0',
  `created_at` int(25) NOT NULL DEFAULT '0',
  `last_post` int(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `glybe_partners` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `type` enum('cat','link') NOT NULL DEFAULT 'link',
  `cat` varchar(225) NOT NULL DEFAULT '6',
  `link` varchar(225) NOT NULL DEFAULT 'www.glybe.nl',
  `name` varchar(225) NOT NULL DEFAULT 'NULL',
  `recommed` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_from_id` int(25) NOT NULL DEFAULT '0',
  `user_to_id` int(25) NOT NULL DEFAULT '0',
  `state` enum('open','deleted','marked_as_spam','important') NOT NULL DEFAULT 'open',
  `readed` enum('true','false') NOT NULL DEFAULT 'false',
  `folder_id` int(25) NOT NULL DEFAULT '0' COMMENT '0 = postvak in',
  `sended_on` int(25) NOT NULL,
  `readed_on` int(25) NOT NULL,
  `subject` varchar(400) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `messages_folders` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `caption` varchar(200) NOT NULL DEFAULT 'Map',
  `icon` varchar(100) NOT NULL DEFAULT 'folder',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `most_online` (
  `date` datetime NOT NULL,
  `number_of_users` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `user_from_id` int(25) NOT NULL DEFAULT '0',
  `icon` varchar(100) NOT NULL DEFAULT 'house',
  `title` varchar(100) NOT NULL DEFAULT 'Belangrijk',
  `message` varchar(5000) NOT NULL DEFAULT 'Bas is uber',
  `url` varchar(1000) NOT NULL DEFAULT '#',
  `is_done` enum('1','0') NOT NULL DEFAULT '0',
  `n_ts` int(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `poll_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(25) NOT NULL DEFAULT '0',
  `caption` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `poll_questions` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `question` varchar(500) NOT NULL DEFAULT 'Vraag',
  `topic_id` int(25) NOT NULL DEFAULT '0' COMMENT '0 = buiten een topic',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `poll_id` int(25) NOT NULL,
  `answer_id` int(25) NOT NULL,
  `user_id` int(25) NOT NULL,
  `voted_on` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_guestbook` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `profile_id` int(25) NOT NULL DEFAULT '0',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `post_timestamp` int(25) NOT NULL,
  `message` text NOT NULL,
  `deleted` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_music` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `ordernum` int(25) NOT NULL DEFAULT '0',
  `yt_id` varchar(11) NOT NULL,
  `trackname` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `status_updates` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `post_timestamp` int(25) NOT NULL,
  `last_update` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `status_updates_replies` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `status_id` int(25) NOT NULL DEFAULT '0',
  `user_id` int(25) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `post_timestamp` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `caption` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `date` int(50) NOT NULL,
  `gelezen` int(2) NOT NULL DEFAULT '0',
  `state` enum('open','closed') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tickets_reactions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `content` text NOT NULL,
  `date` int(50) NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `permission_id` int(25) NOT NULL DEFAULT '1',
  `mail` varchar(500) NOT NULL,
  `avatar` varchar(500) NOT NULL DEFAULT '0_default.png',
  `status_id` enum('1','2','3','4') NOT NULL DEFAULT '1',
  `kladblok` text NOT NULL,
  `warn` int(3) NOT NULL DEFAULT '0',
  `ip` varchar(500) NOT NULL,
  `last_ip` varchar(500) NOT NULL,
  `last_active` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `active` int(3) NOT NULL DEFAULT '0',
  `page_views` int(100) NOT NULL DEFAULT '0',
  `reg_date` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_bans` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `ban_reason` varchar(500) NOT NULL,
  `ban_start` int(25) NOT NULL,
  `ban_expire` int(25) NOT NULL,
  `added_by` int(25) NOT NULL,
  `ip_ban` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_friends` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_one_id` int(10) NOT NULL,
  `user_two_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=450 ;

CREATE TABLE IF NOT EXISTS `users_friends_requests` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_from` int(25) NOT NULL DEFAULT '0',
  `user_to` int(25) NOT NULL DEFAULT '0',
  `date_requested` int(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_online` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL,
  `last_active` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(100) NOT NULL,
  `u_a` varchar(500) NOT NULL,
  `last_page` varchar(500) NOT NULL,
  `last_topic` int(20) NOT NULL,
  `last_forum` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_permissions` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `caption` varchar(100) NOT NULL,
  `volgorde` int(2) NOT NULL DEFAULT '0',
  `on_teampage` enum('false','true') NOT NULL DEFAULT 'true',
  `login` enum('true','false') NOT NULL DEFAULT 'false',
  `is_admin` enum('true','false') NOT NULL DEFAULT 'false',
  `is_team` enum('true','false') NOT NULL DEFAULT 'false',
  `is_support` enum('true','false') NOT NULL DEFAULT 'false',
  `pro` enum('true','false') NOT NULL DEFAULT 'false',
  `see_forum` enum('true','false') NOT NULL DEFAULT 'false',
  `forum_topic_lock` enum('true','false') NOT NULL DEFAULT 'false',
  `forum_topic_delete` enum('true','false') NOT NULL DEFAULT 'false',
  `forum_topic_edit` enum('true','false') NOT NULL DEFAULT 'false',
  `forum_topic_sticky` enum('true','false') NOT NULL DEFAULT 'false',
  `warn_user` enum('false','true') NOT NULL DEFAULT 'false',
  `forum_report` enum('false','true') NOT NULL DEFAULT 'false',
  `forum_post_delete` enum('false','true') NOT NULL DEFAULT 'false',
  `move_topic` enum('true','false') NOT NULL DEFAULT 'false',
  `forum_first_post` enum('false','true') NOT NULL DEFAULT 'false',
  `is_opper_admin` enum('false','true') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_profilepictures` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL,
  `save_file` varchar(200) NOT NULL,
  `thumb_file` varchar(200) NOT NULL,
  `current` enum('true','false') NOT NULL DEFAULT 'false',
  `uploaded` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_profilestyle` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL DEFAULT '0',
  `background` varchar(1000) NOT NULL DEFAULT '',
  `background_repeat` varchar(100) NOT NULL DEFAULT 'no-repeat',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_respectgiven` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL,
  `user_to_id` int(25) NOT NULL,
  `time` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `accept_friend_request` enum('true','false') NOT NULL DEFAULT 'true',
  `accept_group_invite` enum('true','false') NOT NULL DEFAULT 'true',
  `displayname` varchar(50) NOT NULL DEFAULT '',
  `onderschrift` text NOT NULL,
  `profile_story` text NOT NULL,
  `profile_views` int(25) NOT NULL DEFAULT '0',
  `profile_cover` varchar(1000) NOT NULL,
  `respect` int(25) NOT NULL DEFAULT '0',
  `punten` double(100,2) NOT NULL DEFAULT '5.00',
  `send_pb_on_gb_post` enum('true','false') NOT NULL DEFAULT 'true',
  `birthdate` varchar(40) NOT NULL,
  `public_updates` enum('true','false') NOT NULL DEFAULT 'true',
  `sound_notif` enum('true','false') NOT NULL DEFAULT 'true',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_warn` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `user_from` int(10) NOT NULL,
  `percent` int(3) NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  `seen` int(2) NOT NULL DEFAULT '0',
  `date` varchar(50) NOT NULL,
  `ban` int(2) NOT NULL DEFAULT '0',
  `ban_tot` varchar(50) NOT NULL,
  `ipban` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `web_sessions` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `session_hash` varchar(500) NOT NULL,
  `session_start` int(25) NOT NULL,
  `session_end` int(25) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `user_ua` varchar(500) NOT NULL,
  `user_id` int(25) NOT NULL,
  `admin_session` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
