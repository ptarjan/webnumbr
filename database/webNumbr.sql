DROP TABLE IF EXISTS numbr_table;
CREATE TABLE numbr_table (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(63) NOT NULL,
    title VARCHAR(255),
    description VARCHAR(1000),
    url VARCHAR(2000) NOT NULL,
    xpath VARCHAR(2000) NOT NULL,
    frequency INT NOT NULL DEFAULT 1,
    openid VARCHAR(255) NOT NULL,
    goodFetches INT NOT NULL DEFAULT 0,
    badFetches INT NOT NULL DEFAULT 0,
    createdTime TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    modifiedTime TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update NOW(),
    UNIQUE INDEX name(name)
);

DROP TALBE IF EXISTS numbr_names;
DROP TALBE IF EXISTS numbr_names (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    numbr_id INT NOT NULL,
    name VARCHAR(63) NOT NULL,
    FOREIGN KEY (numbr_id) REFERENCES numbr_table(id),
);

DROP FUNCTION IF EXISTS domain;
DELIMITER $$
CREATE FUNCTION domain (url VARCHAR(2000))
    RETURNS VARCHAR(255)
BEGIN
    DECLARE s INT;
    DECLARE e INT;
    set s = LOCATE("/", url) + 1;
    set e = LOCATE("/", url, s + 1);
    IF e = 0 THEN 
        set e = LENGTH(url) + 1;
    END IF;
    RETURN SUBSTRING(url, s + 1, e - s-1);
END;
$$
DELIMITER ;

DROP FUNCTION IF EXISTS short;
DELIMITER $$
CREATE FUNCTION short (s TEXT, len INT)
    RETURNS TEXT
BEGIN
    IF LENGTH(s) > len THEN
        RETURN CONCAT(SUBSTRING(s, 1, len -3), "...");
    END IF;
    RETURN s;
END;
$$
DELIMITER ;

DROP VIEW IF EXISTS numbrs;
CREATE VIEW numbrs AS

SELECT *,
-- 1 week of brokenness
badFetches < 100 OR badFetchesSequential > (24*7 / frequency) AS is_fetching,
domain(url) AS domain

FROM numbr_table;
;

DROP TABLE IF EXISTS numbr_data;
CREATE TABLE numbr_data (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    numbr VARCHAR(63) NOT NULL,
    data FLOAT NOT NULL,
    timestamp TIMESTAMP NOT NULL,
    FOREIGN KEY (numbr) REFERENCES numbrs(name)
);
