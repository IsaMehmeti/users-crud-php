-- Create database
CREATE DATABASE IF NOT EXISTS company_db;
USE company_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Stored Procedure: Create User
DELIMITER //
CREATE PROCEDURE create_user(
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(150),
    IN p_password VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    INSERT INTO users (first_name, last_name, email, password, created_at, updated_at)
    VALUES (p_first_name, p_last_name, p_email, p_password, NOW(), NOW());
    
    SELECT LAST_INSERT_ID() as id, p_first_name as first_name, p_last_name as last_name, 
           p_email as email, NOW() as created_at, NOW() as updated_at;
    
    COMMIT;
END //
DELIMITER ;

-- Stored Procedure: Get User by ID
DELIMITER //
CREATE PROCEDURE get_user_by_id(
    IN p_id INT
)
BEGIN
    SELECT id, first_name, last_name, email, created_at, updated_at
    FROM users
    WHERE id = p_id;
END //
DELIMITER ;

-- Stored Procedure: Get User by Email (for authentication)
DELIMITER //
CREATE PROCEDURE get_user_by_email(
    IN p_email VARCHAR(150)
)
BEGIN
    SELECT id, first_name, last_name, email, password, created_at, updated_at
    FROM users
    WHERE email = p_email;
END //
DELIMITER ;

-- Stored Procedure: Get All Users with Pagination
DELIMITER //
CREATE PROCEDURE get_all_users(
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    IF p_limit IS NULL THEN
        SET p_limit = 10;
    END IF;
    
    IF p_offset IS NULL THEN
        SET p_offset = 0;
    END IF;
    
    SELECT id, first_name, last_name, email, created_at, updated_at
    FROM users
    ORDER BY created_at DESC
    LIMIT p_limit OFFSET p_offset;
END //
DELIMITER ;

-- Stored Procedure: Update User
DELIMITER //
CREATE PROCEDURE update_user(
    IN p_id INT,
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(150),
    IN p_password VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    IF p_password IS NOT NULL AND p_password != '' THEN
        UPDATE users 
        SET first_name = p_first_name, 
            last_name = p_last_name, 
            email = p_email, 
            password = p_password,
            updated_at = NOW()
        WHERE id = p_id;
    ELSE
        UPDATE users 
        SET first_name = p_first_name, 
            last_name = p_last_name, 
            email = p_email,
            updated_at = NOW()
        WHERE id = p_id;
    END IF;
    
    SELECT id, first_name, last_name, email, created_at, updated_at
    FROM users
    WHERE id = p_id;
    
    COMMIT;
END //
DELIMITER ;

-- Stored Procedure: Delete User
DELIMITER //
CREATE PROCEDURE delete_user(
    IN p_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    DELETE FROM users WHERE id = p_id;
    
    COMMIT;
END //
DELIMITER ; 