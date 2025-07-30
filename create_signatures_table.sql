CREATE TABLE `pdf_signatures` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pdf_id` INT UNSIGNED NOT NULL,
  `page_number` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `x` FLOAT NOT NULL,
  `y` FLOAT NOT NULL,
  `width` FLOAT NOT NULL,
  `height` FLOAT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`pdf_id`),
  INDEX (`page_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
