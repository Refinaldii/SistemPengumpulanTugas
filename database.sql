-- Tabel mata praktikum
CREATE TABLE praktikum (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT
);

-- Tabel pendaftaran mahasiswa ke praktikum
CREATE TABLE pendaftaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  praktikum_id INT NOT NULL,
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (praktikum_id) REFERENCES praktikum(id) ON DELETE CASCADE
);


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel modul (per pertemuan)
CREATE TABLE modul (
  id INT AUTO_INCREMENT PRIMARY KEY,
  praktikum_id INT NOT NULL,
  judul VARCHAR(100),
  materi_file VARCHAR(255),
  FOREIGN KEY (praktikum_id) REFERENCES praktikum(id) ON DELETE CASCADE
);

-- Tabel laporan mahasiswa
CREATE TABLE laporan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  modul_id INT,
  file_laporan VARCHAR(255),
  nilai INT DEFAULT NULL,
  feedback TEXT,
  tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (modul_id) REFERENCES modul(id)
);

CREATE TABLE praktikum (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT
);

CREATE TABLE modul (
  id INT AUTO_INCREMENT PRIMARY KEY,
  praktikum_id INT NOT NULL,
  judul VARCHAR(100),
  materi_file VARCHAR(255),
  FOREIGN KEY (praktikum_id) REFERENCES praktikum(id) ON DELETE CASCADE
);

-- Tabel laporan mahasiswa
CREATE TABLE laporan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  modul_id INT,
  file_laporan VARCHAR(255),
  nilai INT DEFAULT NULL,
  feedback TEXT,
  tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (modul_id) REFERENCES modul(id)
);
