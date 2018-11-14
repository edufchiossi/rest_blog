SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `category` (
	`id_category` int(11) NOT NULL,
	`name` varchar(50) NOT NULL,
	`description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `post` (
	`id_post` int(11) NOT NULL,
	`id_category` int(11) NOT NULL,
	`title` varchar(200) NOT NULL,
	`text` text NOT NULL,
	`author` varchar(150) NOT NULL,
	`creation_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `category`
	ADD PRIMARY KEY (`id_category`);

ALTER TABLE `post`
	ADD PRIMARY KEY (`id_post`),
	ADD KEY `id_category` (`id_category`);

ALTER TABLE `category`
	MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `post`
	MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `post`
	ADD CONSTRAINT `fk_category` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`);
COMMIT;