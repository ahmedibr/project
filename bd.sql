USE c9;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `login` varchar(255) PRIMARY KEY,
  `pass` varchar(255) NOT NULL,
  );
  
  
  
INSERT INTO `users` VALUES ('imene','imene');
