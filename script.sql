-- -----------------------------------------------------
-- Schema mvc
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mvc` DEFAULT CHARACTER SET utf8 ;
USE `mvc` ;

-- -----------------------------------------------------
-- Table `mvc`.`Users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`Users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(255) NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `surname` VARCHAR(80) NOT NULL,
  `hash` VARCHAR(255) NOT NULL,
  `organization` VARCHAR(255) NOT NULL,
  `email` VARCHAR(80) NOT NULL,
  `phoneNumber` VARCHAR(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `login_UNIQUE` (`login` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`Products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sku` INT(11) NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `price` FLOAT NOT NULL,
  `size` INT(11) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `UserProduct_idx` (`userId` ASC) VISIBLE,
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
CREATE TABLE IF NOT EXISTS `mvc`.`Warehouses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `address` VARCHAR(255) NOT NULL,
  `capacity` INT(11) NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `UserWarehouse_idx` (`userId` ASC) VISIBLE,
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
CREATE TABLE IF NOT EXISTS `mvc`.`State` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `warehouseId` INT(11) NOT NULL,
  `productId` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `date` DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `WarehouseState_idx` (`warehouseId` ASC) VISIBLE,
  INDEX `Product_idx` (`productId` ASC) VISIBLE,
  CONSTRAINT `Product`
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
CREATE TABLE IF NOT EXISTS `mvc`.`Transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `productId` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `direction` VARCHAR(80) NOT NULL,
  `datetime` DATETIME NOT NULL,
  `sender` VARCHAR(255) NULL DEFAULT NULL,
  `recipient` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `ProductTransaction_idx` (`productId` ASC) VISIBLE,
  CONSTRAINT `ProductTransaction`
    FOREIGN KEY (`productId`)
    REFERENCES `mvc`.`Products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE USER 'non-root'@'%' IDENTIFIED BY '12345';

GRANT SELECT, INSERT, DELETE, UPDATE ON mvc.Users TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE, UPDATE ON mvc.Products TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE, UPDATE ON mvc.Warehouses TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE ON mvc.Transactions TO 'non-root'@'%';
GRANT SELECT, INSERT, DELETE, UPDATE ON mvc.State TO 'non-root'@'%';

FLUSH PRIVILEGES;
