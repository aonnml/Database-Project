CREATE DATABASE IF NOT EXISTS shipphi;
USE shipphi;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    email VARCHAR(150) NOT NULL UNIQUE,
    profile_image VARCHAR(255) DEFAULT 'image/user.jpg',
    phoneNum VARCHAR(15),
    password VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS category(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS product (
    id  INT AUTO_INCREMENT PRIMARY KEY,
    categoryId INT,
    name VARCHAR(150) NOT NULL,
    price INT NOT NULL,
    size VARCHAR(10),
    description text,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    FOREIGN KEY (categoryId) REFERENCES category(id)
);

CREATE TABLE IF NOT EXISTS review(
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT,
    productId INT,
    rate FLOAT NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description text,
    FOREIGN KEY (userId) REFERENCES users(id),
    FOREIGN KEY (productId) REFERENCES product(id)
);

ALTER TABLE review ADD COLUMN is_reviewed TINYINT(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userId INT NOT NULL,
  productId INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  FOREIGN KEY (userId) REFERENCES users(id),
  FOREIGN KEY (productId) REFERENCES product(id)
);

INSERT INTO `category` (`id`, `name`) VALUES
  ('1','Home'),
  ('2','Garden'),
  ('3','Fashion'),
  ('4','Makeup'),
  ('5','Electronic'),
  ('6','HealthCare'),
  ('7','Food'),
  ('8','Education');
