CREATE DATABASE IF NOT EXISTS study_materials;
USE study_materials;

CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    semester VARCHAR(50) NOT NULL,
    material_type VARCHAR(100) NOT NULL,
    uploaded_by VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL
);