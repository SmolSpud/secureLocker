CREATE DATABASE locker_system;
GO
USE locker_system;
GO

SELECT * FROM users;
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY IDENTITY(1,1),
    student_id VARCHAR(7) UNIQUE NOT NULL, -- format: XX-XXXX
    last_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    first_name VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(10) NOT NULL CHECK (role IN ('student','admin'))
);

-- Lockers table
CREATE TABLE lockers (
    id INT PRIMARY KEY IDENTITY(1,1),
    department VARCHAR(20) NOT NULL,
    floor INT NOT NULL,
    status VARCHAR(10) NOT NULL CHECK (status IN ('available','occupied')) DEFAULT 'available',
    owner_id INT FOREIGN KEY REFERENCES users(id)
);

-- Reservations table
CREATE TABLE reservations (
    id INT PRIMARY KEY IDENTITY(1,1),
    locker_id INT NOT NULL FOREIGN KEY REFERENCES lockers(id),
    user_id INT NOT NULL FOREIGN KEY REFERENCES users(id),
    reserved_at DATETIME DEFAULT GETDATE()
);

---------------------------------------------------------
-- Populate lockers: 8 departments × 6 floors × 20 lockers
---------------------------------------------------------

DECLARE @departments TABLE (dept VARCHAR(20));
INSERT INTO @departments VALUES
('CABA'),('CEIT'),('COED'),('CPAG'),
('NB'),('CAS'),('ENG'),('MED');



CREATE TABLE #departments (dept VARCHAR(20));
INSERT INTO #departments VALUES
('CABA'),('CEIT'),('COED'),('CPAG'),
('NB'),('CAS'),('ENG'),('MED');

DECLARE @dept VARCHAR(20);

DECLARE dept_cursor CURSOR FOR SELECT dept FROM #departments;
OPEN dept_cursor;
FETCH NEXT FROM dept_cursor INTO @dept;

WHILE @@FETCH_STATUS = 0
BEGIN
    DECLARE @floor INT = 1;
    WHILE @floor <= 6
    BEGIN
        DECLARE @i INT = 1;
        WHILE @i <= 20
        BEGIN
            INSERT INTO lockers (department, floor, status, owner_id)
            VALUES (@dept, @floor, 'available', NULL);
            SET @i = @i + 1;
        END
        SET @floor = @floor + 1;
    END
    FETCH NEXT FROM dept_cursor INTO @dept;
END




USE master;
GO

-- Create a SQL login with a strong password
CREATE LOGIN locker_user WITH PASSWORD = 'StrongPassword123!';

-- Map it to your locker_system database
USE locker_system;
GO
CREATE USER locker_user FOR LOGIN locker_user;

-- Give it rights (start with db_owner for testing)
ALTER ROLE db_owner ADD MEMBER locker_user;

CLOSE dept_cursor;
DEALLOCATE dept_cursor;