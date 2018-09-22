-- -----------------------------------------------------
-- Schema mvc
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mvc`;
CREATE SCHEMA `mvc` DEFAULT CHARACTER SET utf8;
USE `mvc`;
-- -----------------------------------------------------
-- Table `mvc`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mvc`.`Users`;
CREATE TABLE `mvc`.`Users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(255) NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `surname` VARCHAR(80) NOT NULL,
  `hash` VARCHAR(255) NOT NULL,
  `organization` VARCHAR(255) NOT NULL,
  `email` VARCHAR(80) NOT NULL,
  `phoneNumber` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  UNIQUE INDEX `login_UNIQUE` (`login` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mvc`.`Products`;
CREATE TABLE `mvc`.`Products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sku` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `price` FLOAT NOT NULL,
  `size` INT(11) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `UserProduct_idx` (`userId` ASC),
  CONSTRAINT `UserProduct`
    FOREIGN KEY (`userId`)
    REFERENCES `mvc`.`Users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Warehouses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mvc`.`Warehouses`;
CREATE TABLE `mvc`.`Warehouses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `address` VARCHAR(255) NOT NULL,
  `capacity` INT(11) NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `UserWarehouse_idx` (`userId` ASC),
  CONSTRAINT `UserWarehouse`
    FOREIGN KEY (`userId`)
    REFERENCES `mvc`.`Users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`State`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mvc`.`State`;
CREATE TABLE `mvc`.`State` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `warehouseId` INT(11) NOT NULL,
  `productId` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `date` DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `WarehouseState_idx` (`warehouseId` ASC),
  INDEX `ProductState_idx` (`productId` ASC),
  CONSTRAINT `ProductState`
    FOREIGN KEY (`productId`)
    REFERENCES `mvc`.`Products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `WarehouseState`
    FOREIGN KEY (`warehouseId`)
    REFERENCES `mvc`.`Warehouses` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Transactions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mvc`.`Transactions`;
CREATE TABLE `mvc`.`Transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `productId` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `direction` VARCHAR(80) NOT NULL,
  `datetime` DATETIME NOT NULL,
  `sender` VARCHAR(255) NULL DEFAULT NULL,
  `recipient` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `ProductTransaction_idx` (`productId` ASC),
  CONSTRAINT `ProductTransaction`
    FOREIGN KEY (`productId`)
    REFERENCES `mvc`.`Products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE USER 'non-root'@'%' IDENTIFIED BY '12345';

GRANT SELECT, INSERT, DELETE, UPDATE ON `mvc`.`Users` TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE, UPDATE ON `mvc`.`Products` TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE, UPDATE ON `mvc`.`Warehouses` TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE ON `mvc`.`Transactions` TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE ON `mvc`.`State` TO 'non-root'@'%';

FLUSH PRIVILEGES;
