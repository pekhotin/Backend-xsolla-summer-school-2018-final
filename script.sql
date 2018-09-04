-- -----------------------------------------------------
-- Schema mvc
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mvc` DEFAULT CHARACTER SET utf8 ;
USE `mvc` ;

-- -----------------------------------------------------
-- Table `mvc`.`Products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`Products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(225) NOT NULL,
  `price` FLOAT NOT NULL,
  `size` INT(11) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Warehouses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`Warehouses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `address` VARCHAR(255) NOT NULL,
  `capacity` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `address_UNIQUE` (`address` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`ProductBatches`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`ProductBatches` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idProduct` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `idWarehouse` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `idWarehouse_idx` (`idWarehouse` ASC) VISIBLE,
  INDEX `idProduct_idx` (`idProduct` ASC) VISIBLE,
  CONSTRAINT `idProduct`
    FOREIGN KEY (`idProduct`)
    REFERENCES `mvc`.`Products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idWarehouse`
    FOREIGN KEY (`idWarehouse`)
    REFERENCES `mvc`.`Warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Transactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`Transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idWarehouse` INT(11) NOT NULL,
  `idProduct` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `direction` VARCHAR(45) NOT NULL,
  `date` DATETIME NULL DEFAULT NULL,
  `sender` VARCHAR(255) NULL DEFAULT NULL,
  `recipient` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `transactionWithWarehouse_idx` (`idWarehouse` ASC) VISIBLE,
  INDEX `transactionWithProduct_idx` (`idProduct` ASC) VISIBLE,
  CONSTRAINT `transactionWithProduct`
    FOREIGN KEY (`idProduct`)
    REFERENCES `mvc`.`Products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transactionWithWarehouse`
    FOREIGN KEY (`idWarehouse`)
    REFERENCES `mvc`.`Warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mvc`.`Users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc`.`Users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `organization` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `phoneNumber` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idUsers_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;