DROP DATABASE IF EXISTS ecommerce_prodavnica;

CREATE DATABASE ecommerce_prodavnica
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ecommerce_prodavnica;


-- ======================
-- KORISNICI
-- ======================

CREATE TABLE korisnici (

    korisnik_id INT AUTO_INCREMENT PRIMARY KEY,

    ime VARCHAR(50) NOT NULL,

    prezime VARCHAR(50) NOT NULL,

    email VARCHAR(100) UNIQUE NOT NULL,

    lozinka VARCHAR(255) NOT NULL,

    telefon VARCHAR(20),

    adresa VARCHAR(255),

    grad VARCHAR(50),

    postanski_broj VARCHAR(10),

    uloga ENUM('ADMIN','KUPAC') DEFAULT 'KUPAC',

    datum_registracije DATETIME DEFAULT CURRENT_TIMESTAMP

);



-- ======================
-- KATEGORIJE
-- ======================

CREATE TABLE kategorije (

    kategorija_id INT AUTO_INCREMENT PRIMARY KEY,

    naziv VARCHAR(100) NOT NULL,

    opis TEXT

);



INSERT INTO kategorije(naziv) VALUES

('Laptop'),
('Desktop računari'),
('Monitori'),
('Grafičke kartice'),
('Procesori'),
('Matične ploče'),
('RAM memorije'),
('SSD diskovi'),
('Napajanja'),
('Tastature'),
('Miševi'),
('Slušalice'),
('Kućišta'),
('Web kamere');



-- ======================
-- BRENDOVI
-- ======================

CREATE TABLE brendovi (

    brend_id INT AUTO_INCREMENT PRIMARY KEY,

    naziv VARCHAR(100) NOT NULL

);



INSERT INTO brendovi(naziv) VALUES

('ASUS'),
('MSI'),
('Gigabyte'),
('Dell'),
('HP'),
('Lenovo'),
('Logitech'),
('Intel'),
('AMD'),
('Samsung'),
('Kingston'),
('Corsair'),
('Razer');



-- ======================
-- PROIZVODI
-- ======================

CREATE TABLE proizvodi (

    proizvod_id INT AUTO_INCREMENT PRIMARY KEY,

    naziv VARCHAR(150) NOT NULL,

    opis TEXT,

    cena DECIMAL(10,2) NOT NULL,

    stanje INT DEFAULT 0,

    slika VARCHAR(255),

    kategorija_id INT,

    brend_id INT,


    FOREIGN KEY(kategorija_id)
    REFERENCES kategorije(kategorija_id)
    ON DELETE SET NULL,


    FOREIGN KEY(brend_id)
    REFERENCES brendovi(brend_id)
    ON DELETE SET NULL

);



-- ======================
-- KORPA
-- ======================

CREATE TABLE korpa (

    korpa_id INT AUTO_INCREMENT PRIMARY KEY,

    korisnik_id INT NOT NULL,

    proizvod_id INT NOT NULL,

    kolicina INT DEFAULT 1,

    datum_dodavanja DATETIME DEFAULT CURRENT_TIMESTAMP,


    FOREIGN KEY(korisnik_id)
    REFERENCES korisnici(korisnik_id)
    ON DELETE CASCADE,


    FOREIGN KEY(proizvod_id)
    REFERENCES proizvodi(proizvod_id)
    ON DELETE CASCADE

);



-- ======================
-- PORUDZBINE
-- ======================


CREATE TABLE porudzbine (

    porudzbina_id INT AUTO_INCREMENT PRIMARY KEY,

    korisnik_id INT NOT NULL,

    ime VARCHAR(100) NOT NULL,

    prezime VARCHAR(100) NOT NULL,

    adresa VARCHAR(255) NOT NULL,

    grad VARCHAR(100) NOT NULL,

    telefon VARCHAR(50) NOT NULL,

    ukupna_cena DECIMAL(10,2) NOT NULL,

    status ENUM(
        'Na čekanju',
        'U obradi',
        'Poslato',
        'Isporučeno'
    )
    DEFAULT 'Na čekanju',

    datum DATETIME DEFAULT CURRENT_TIMESTAMP,


    FOREIGN KEY(korisnik_id)
    REFERENCES korisnici(korisnik_id)

);





CREATE TABLE orders (

    id INT AUTO_INCREMENT PRIMARY KEY,

    korisnik_id INT,

    ukupno DECIMAL(10,2),

    status VARCHAR(50),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    

    FOREIGN KEY(korisnik_id)
    REFERENCES korisnici(korisnik_id)

);



-- ======================
-- STAVKE PORUDŽBINE
-- ======================

CREATE TABLE stavke_porudzbine (

    stavka_id INT AUTO_INCREMENT PRIMARY KEY,

    porudzbina_id INT NOT NULL,

    proizvod_id INT,

    naziv_proizvoda VARCHAR(150) NOT NULL,

    slika_proizvoda VARCHAR(255),

    kolicina INT NOT NULL,

    cena DECIMAL(10,2) NOT NULL,


    FOREIGN KEY(porudzbina_id)
    REFERENCES porudzbine(porudzbina_id)
    ON DELETE CASCADE

);

CREATE TABLE reviews (

    review_id INT AUTO_INCREMENT PRIMARY KEY,

    korisnik_id INT NOT NULL,

    proizvod_id INT NOT NULL,

    ocena INT NOT NULL,

    komentar TEXT,

    datum DATETIME DEFAULT CURRENT_TIMESTAMP,


    FOREIGN KEY(korisnik_id)
    REFERENCES korisnici(korisnik_id)
    ON DELETE CASCADE,


    FOREIGN KEY(proizvod_id)
    REFERENCES proizvodi(proizvod_id)
    ON DELETE CASCADE

);

CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    korisnik_id INT NOT NULL,
    proizvod_id INT NOT NULL,
    datum_dodavanja DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (korisnik_id) REFERENCES korisnici(korisnik_id) ON DELETE CASCADE,
    FOREIGN KEY (proizvod_id) REFERENCES proizvodi(proizvod_id) ON DELETE CASCADE,

    UNIQUE (korisnik_id, proizvod_id)
);

CREATE TABLE shop_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    show_sales_chart TINYINT(1) DEFAULT 1,
    show_latest_orders TINYINT(1) DEFAULT 1,
    order_notification TINYINT(1) DEFAULT 1,
    stock_notification TINYINT(1) DEFAULT 1,
    login_attempts INT DEFAULT 5
);


INSERT INTO shop_settings
(show_sales_chart, show_latest_orders, order_notification, stock_notification, login_attempts)
VALUES
(1,1,1,1,5);


INSERT INTO proizvodi
(naziv, opis, cena, stanje, slika, kategorija_id, brend_id)
VALUES


('MSI Katana GF66',
'Gaming laptop Intel Core i7 RTX3060 16GB RAM SSD 512GB 15.6 inch',
139999,
6,
'msi-katana-gf66.jpg',
1,
2),


('Gigabyte Aero 15',
'Premium gaming laptop OLED ekran RTX3070 32GB RAM SSD 1TB',
169999,
4,
'gigabyte-aero15.jpg',
1,
3),


('Acer Nitro 5',
'Gaming laptop Ryzen 7 RTX3060 16GB RAM SSD 512GB',
109999,
8,
'acer-nitro5.jpg',
1,
5),


('HP Pavilion Gaming',
'Gaming laptop Intel i5 GTX1650 16GB RAM SSD 512GB',
89999,
10,
'hp-pavilion-gaming.jpg',
1,
5),


('Lenovo IdeaPad Gaming 3',
'Gaming laptop Ryzen 5 RTX3050 SSD 512GB',
99999,
12,
'lenovo-ideapad-gaming.jpg',
1,
6),


('Dell Inspiron 15',
'Laptop Intel i5 8GB RAM SSD 512GB Full HD ekran',
74999,
15,
'dell-inspiron15.jpg',
1,
4),


('ASUS ROG Strix G16',
'Gaming laptop Intel i9 RTX4070 32GB RAM SSD 1TB',
219999,
3,
'asus-rog-strix-g16.jpg',
1,
1),


('Lenovo Legion 5',
'Gaming laptop Ryzen 7 RTX4060 16GB RAM',
159999,
5,
'lenovo-legion-5.jpg',
1,
6),


('HP Victus 16',
'Gaming laptop Intel i7 RTX4050 16GB RAM SSD',
129999,
7,
'hp-victus16.jpg',
1,
5),


('ASUS TUF Gaming F15',
'Gaming laptop Intel i7 RTX4060 16GB RAM',
149999,
6,
'asus-tuf-f15.jpg',
1,
1),



('HP EliteDesk 800 G6',
'Office desktop Intel i7 16GB RAM SSD 512GB',
89999,
8,
'hp-elitedesk800.jpg',
2,
5),


('Dell Precision 5820',
'Profesionalni desktop Xeon procesor 32GB RAM SSD',
149999,
5,
'dell-precision.jpg',
2,
4),


('ASUS ROG Desktop G10',
'Gaming desktop RTX4060 32GB RAM SSD 1TB',
179999,
3,
'asus-rog-desktop.jpg',
2,
1),


('MSI MAG Infinite S3',
'Gaming računar Intel i7 RTX4070 32GB RAM',
199999,
4,
'msi-mag-infinite.jpg',
2,
2),


('Lenovo ThinkCentre M90',
'Poslovni desktop Intel i5 16GB RAM SSD',
79999,
9,
'lenovo-thinkcentre.jpg',
2,
6),


('Dell Optiplex 7090',
'Office računar Intel i7 16GB RAM',
99999,
6,
'dell-optiplex.jpg',
2,
4),



('Samsung Odyssey G7',
'Gaming monitor 32 inch 240Hz QHD zakrivljeni ekran',
69999,
7,
'samsung-odyssey-g7.jpg',
3,
10),


('ASUS TUF VG27AQ',
'Gaming monitor 27 inch 165Hz IPS QHD',
44999,
10,
'asus-tuf-monitor.jpg',
3,
1),


('MSI Optix MAG274QRF',
'Gaming monitor 27 inch QHD 165Hz IPS',
52999,
8,
'msi-optix-mag.jpg',
3,
2),


('Gigabyte M27Q',
'Gaming monitor 27 inch 170Hz IPS',
49999,
7,
'gigabyte-m27q.jpg',
3,
3),


('Dell UltraSharp U2723QE',
'Profesionalni 27 inch 4K monitor',
89999,
5,
'dell-ultrasharp.jpg',
3,
4),


('LG UltraGear 27GN800',
'Gaming monitor 27 inch 144Hz IPS',
45999,
8,
'lg-ultragear.jpg',
3,
10),



('ASUS RTX 3080 TUF',
'Nvidia RTX3080 10GB GDDR6X grafička karta',
79999,
5,
'rtx3080-asus.jpg',
4,
1),


('MSI RTX 4060 Ventus',
'RTX4060 8GB GDDR6 grafička karta',
55999,
10,
'msi-rtx4060.jpg',
4,
2),


('Gigabyte RTX 3060 Eagle',
'RTX3060 12GB grafička karta',
45999,
12,
'gigabyte-rtx3060.jpg',
4,
3),

('ASUS RTX 4090 ROG Strix',
'Premium RTX4090 24GB GDDR6X grafička karta za ekstremni gaming',
249999,
2,
'rog-rtx4090.jpg',
4,
1),


('MSI RTX 4070 Gaming X Trio',
'RTX4070 12GB GDDR6X gaming grafička karta',
99999,
6,
'msi-rtx4070.jpg',
4,
2),


('Gigabyte RTX 4080 Eagle',
'RTX4080 16GB grafička karta visoke klase',
159999,
3,
'gigabyte-rtx4080.jpg',
4,
3),


('ASUS RX 7800 XT',
'AMD Radeon RX7800 XT 16GB grafička karta',
89999,
5,
'asus-rx7800xt.jpg',
4,
1),


('MSI RX 7600 Mech',
'AMD Radeon RX7600 8GB GDDR6 grafička karta',
44999,
10,
'msi-rx7600.jpg',
4,
2),



('Intel Core i5 12400F',
'Gaming procesor 6 jezgara 12 threadova',
19999,
25,
'i5-12400f.jpg',
5,
8),


('Intel Core i7 13700K',
'Procesor 16 jezgara za gaming i profesionalni rad',
59999,
8,
'i7-13700k.jpg',
5,
8),


('Intel Core i9 13900K',
'Profesionalni procesor 24 jezgra visoke performanse',
79999,
5,
'i9-13900k.jpg',
5,
8),


('AMD Ryzen 5 5600X',
'Gaming procesor 6 jezgara AM4 platforma',
18999,
20,
'ryzen-5600x.jpg',
5,
9),


('AMD Ryzen 7 7800X3D',
'Najbolji gaming procesor sa 3D V-Cache tehnologijom',
54999,
7,
'ryzen-7800x3d.jpg',
5,
9),


('AMD Ryzen 9 7950X',
'Profesionalni procesor 16 jezgara AM5',
79999,
4,
'ryzen-7950x.jpg',
5,
9),



('ASUS ROG STRIX B550-F',
'Gaming matična ploča AM4 DDR4 PCI Express',
29999,
10,
'asus-b550.jpg',
6,
1),


('MSI MAG B660 Tomahawk',
'Gaming matična ploča Intel LGA1700 DDR4',
32999,
8,
'msi-b660.jpg',
6,
2),


('Gigabyte Z790 Gaming X',
'Premium Intel Z790 matična ploča DDR5',
49999,
5,
'gigabyte-z790.jpg',
6,
3),


('ASUS TUF B650 Plus',
'AMD AM5 DDR5 gaming matična ploča',
39999,
6,
'asus-b650.jpg',
6,
1),



('Kingston Fury Beast 16GB DDR4',
'16GB RAM memorija 3200MHz DDR4',
6999,
30,
'kingston-ddr4-16.jpg',
7,
11),


('Kingston Fury Beast 32GB DDR5',
'32GB DDR5 RAM memorija 5600MHz',
13999,
20,
'kingston-ddr5-32.jpg',
7,
11),


('Corsair Vengeance 16GB DDR4',
'Gaming RAM 16GB DDR4 3200MHz',
7999,
25,
'corsair-ddr4.jpg',
7,
12),


('Corsair Dominator 32GB DDR5',
'Premium DDR5 RAM 32GB RGB',
18999,
10,
'corsair-dominator.jpg',
7,
12),


('G.Skill Trident Z 32GB',
'Gaming RAM DDR5 RGB memorija',
19999,
8,
'gskill-trident.jpg',
7,
12),



('Samsung 980 Pro 1TB SSD',
'NVMe SSD disk 1TB PCIe 4.0 velike brzine',
15999,
15,
'samsung-980pro.jpg',
8,
10),


('Kingston NV2 1TB SSD',
'NVMe SSD 1TB M.2 disk',
8999,
25,
'kingston-nv2.jpg',
8,
11),


('WD Black SN850X 2TB',
'Gaming NVMe SSD 2TB visoke performanse',
29999,
7,
'wd-black.jpg',
8,
11),


('Samsung 870 EVO 1TB',
'SATA SSD disk 1TB',
9999,
18,
'samsung-870.jpg',
8,
10),


('Crucial MX500 1TB',
'Pouzdan SATA SSD 1TB',
8999,
20,
'crucial-mx500.jpg',
8,
11),



('Corsair RM850x',
'850W modularno napajanje 80 Plus Gold',
19999,
8,
'corsair-rm850.jpg',
9,
12),


('MSI MPG A750GF',
'750W gaming napajanje Gold sertifikat',
15999,
10,
'msi-a750.jpg',
9,
2),


('ASUS ROG Thor 1000W',
'Premium 1000W RGB napajanje',
39999,
3,
'asus-thor.jpg',
9,
1),


('Gigabyte P850GM',
'850W modularno napajanje',
14999,
7,
'gigabyte-p850.jpg',
9,
3),


('Cooler Master MWE 650',
'650W kvalitetno napajanje',
9999,
15,
'cooler-master-650.jpg',
9,
12),


('Logitech G Pro X Keyboard',
'Profesionalna mehanička gaming tastatura RGB',
15999,
12,
'logitech-g-pro-keyboard.jpg',
10,
7),


('Razer BlackWidow V3',
'Mehanička gaming tastatura Razer RGB',
18999,
8,
'razer-blackwidow.jpg',
10,
7),


('Corsair K70 RGB',
'Premium mehanička tastatura Cherry MX svičevi',
21999,
6,
'corsair-k70.jpg',
10,
12),


('ASUS ROG Strix Scope',
'Gaming mehanička tastatura RGB',
14999,
10,
'asus-scope.jpg',
10,
1),


('MSI Vigor GK50',
'Gaming tastatura sa RGB osvetljenjem',
8999,
15,
'msi-gk50.jpg',
10,
2),



('Logitech G502 Hero',
'Gaming miš 25600 DPI HERO senzor',
7999,
20,
'logitech-g502.jpg',
11,
7),


('Razer DeathAdder V3',
'Profesionalni esports gaming miš',
11999,
12,
'razer-deathadder.jpg',
11,
13),


('Corsair Dark Core RGB',
'Bežični gaming miš RGB osvetljenje',
13999,
8,
'corsair-darkcore.jpg',
11,
12),


('MSI Clutch GM41',
'Lagani gaming miš 16000 DPI',
6999,
14,
'msi-gm41.jpg',
11,
2),


('ASUS ROG Gladius III',
'Gaming miš sa preciznim senzorom',
9999,
10,
'asus-gladius.jpg',
11,
1),



('Razer BlackShark V2',
'Gaming slušalice THX Spatial Audio mikrofon',
12999,
15,
'razer-blackshark.jpg',
12,
13),


('Logitech G Pro X Headset',
'Profesionalne gaming slušalice sa mikrofonom',
14999,
10,
'logitech-gpro-headset.jpg',
12,
7),


('Corsair HS80 RGB',
'Bežične gaming slušalice RGB',
17999,
7,
'corsair-hs80.jpg',
12,
12),


('HyperX Cloud II',
'Popularne gaming slušalice 7.1 zvuk',
9999,
20,
'hyperx-cloud2.jpg',
12,
7),


('ASUS ROG Delta',
'Premium RGB gaming slušalice USB',
19999,
5,
'asus-rog-delta.jpg',
12,
1),



('NZXT H510',
'Mid Tower kućište sa kaljenim staklom',
12999,
10,
'nzxt-h510.jpg',
13,
12),


('Corsair 4000D Airflow',
'Gaming kućište odličan protok vazduha',
14999,
12,
'corsair-4000d.jpg',
13,
12),


('Cooler Master TD500 Mesh',
'RGB gaming kućište sa ventilatorima',
16999,
8,
'cooler-master-td500.jpg',
13,
12),


('MSI MAG Forge 100R',
'Gaming kućište ARGB ventilatori',
9999,
15,
'msi-forge.jpg',
13,
2),


('ASUS TUF GT501',
'Premium gaming kućište',
21999,
4,
'asus-gt501.jpg',
13,
1),



('Logitech C920 HD Pro',
'Full HD web kamera 1080p',
8999,
20,
'logitech-c920.jpg',
14,
7),


('Razer Kiyo',
'Streaming web kamera sa osvetljenjem',
11999,
10,
'razer-kiyo.jpg',
14,
13),


('ASUS ROG Eye',
'Gaming Full HD web kamera',
9999,
8,
'asus-rog-eye.jpg',
14,
1),


('HP 960 4K Webcam',
'Profesionalna 4K web kamera',
15999,
5,
'hp-960-webcam.jpg',
14,
5),


('Logitech Brio 4K',
'Premium 4K Ultra HD web kamera',
19999,
6,
'logitech-brio.jpg',
14,
7);