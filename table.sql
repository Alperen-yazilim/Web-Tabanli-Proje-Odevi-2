CREATE TABLE kullanicilar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    spec VARCHAR(50)
);

CREATE TABLE has_Hasta (
    has_ID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    TC_no VARCHAR(11) UNIQUE NOT NULL,
    birth_date DATE,
    sex VARCHAR(10),
    bld_type VARCHAR(5),
    kullanici_id INT,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE
);

CREATE TABLE mua_Muayene (
    mua_ID INT PRIMARY KEY AUTO_INCREMENT,
    diag VARCHAR(255) NOT NULL,
    notes TEXT,
    date DATE NOT NULL,
    has_ID INT,
    FOREIGN KEY (has_ID) REFERENCES has_Hasta(has_ID) ON DELETE CASCADE
);