CREATE DATABASE IF NOT EXISTS shipphi;
USE shipphi;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    email VARCHAR(150) NOT NULL UNIQUE
);

ALTER TABLE users
ADD phoneNum VARCHAR(15),
ADD password VARCHAR(100) NOT NULL;

CREATE TABLE IF NOT EXISTS saler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phoneNum VARCHAR(15) NOT NULL
);

CREATE TABLE IF NOT EXISTS order_header (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT,
    salerId INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    totalPrice INT NOT NULL, 
    FOREIGN KEY (userId) REFERENCES users(id),
    FOREIGN KEY (salerId) REFERENCES saler(id) 
);

CREATE TABLE IF NOT EXISTS category(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS product (
    id  INT AUTO_INCREMENT PRIMARY KEY,
    categoryId INT,
    name VARCHAR(50) NOT NULL,
    price INT NOT NULL,
    size VARCHAR(10),
    FOREIGN KEY (categoryId) REFERENCES category(id)
);

CREATE TABLE IF NOT EXISTS order_detail (
    orderId INT ,
    productId INT,
    quantity INT NOT NULL,
    FOREIGN KEY (orderId) REFERENCES order_header(id),
    FOREIGN KEY (productId) REFERENCES product(id)
);

CREATE TABLE IF NOT EXISTS review(
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT,
    productId INT,
    rate FLOAT NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description VARCHAR(100),
    FOREIGN KEY (userId) REFERENCES users(id),
    FOREIGN KEY (productId) REFERENCES product(id)
);
