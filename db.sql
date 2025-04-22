-- Create the database
CREATE DATABASE IF NOT EXISTS todo_list;

-- Use the database
USE todo_list;

-- Create the tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'Belum Selesai',
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data
INSERT INTO tasks (judul, deskripsi, status, tanggal_mulai, tanggal_selesai) VALUES
('Tugas IPS', 'Geografi', 'Selesai', '2025-02-03', '2025-03-05'),
('Tugas Matematika', 'Kalkulus Dasar', 'Belum Selesai', '2025-03-10', '2025-03-17'),
('Proyek Penelitian', 'Membuat penelitian tentang lingkungan', 'Belum Selesai', '2025-03-01', '2025-04-15'),
('Presentasi Biologi', 'Presentasi tentang ekosistem laut', 'Selesai', '2025-02-20', '2025-03-01'),
('Tugas Bahasa', 'Menulis esai 5 paragraf', 'Belum Selesai', '2025-03-08', '2025-03-15');


-- masukin ke table tasks
ALTER TABLE tasks ADD COLUMN user_id INT NOT NULL AFTER id;