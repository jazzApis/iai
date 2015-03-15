-- Zakładanie tabeli do zadania 
CREATE TABLE `jz_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identyfikator rekordu',
  `pi` int(11) NOT NULL DEFAULT '0' COMMENT 'Wskazanie rodzica (parentId)',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT 'Ranking ',
  `text` varchar(64) COLLATE utf8_polish_ci NOT NULL COMMENT 'Treść',
  `cTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Czas utworzenia',
  `mTime` timestamp NULL DEFAULT NULL COMMENT 'Czas ostatniej aktualizacji',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `text_UNIQUE` (`text`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci COMMENT='Zadanie testowe j.zbikowski';
