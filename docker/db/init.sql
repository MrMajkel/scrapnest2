
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    imie VARCHAR(50),
    nazwisko VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    haslo TEXT NOT NULL,
    rola VARCHAR(50) NOT NULL
);

CREATE TABLE kontrahenci (
    id SERIAL PRIMARY KEY,
    nazwa_firmy VARCHAR(100),
    bdo VARCHAR(10),
    nip CHAR(10) CHECK (nip ~ '^\d{10}$'),
    adres VARCHAR(255),
    telefon VARCHAR(20),
    mail VARCHAR(100)
);

CREATE TABLE faktury_zakupowe (
    id SERIAL PRIMARY KEY,
    nr_faktury VARCHAR(50) NOT NULL,
    data DATE NOT NULL,
    firma VARCHAR(100) NOT NULL
);

CREATE TABLE pozycje_faktury_zakupowe (
    id SERIAL PRIMARY KEY,
    faktura_id INTEGER NOT NULL REFERENCES faktury_zakupowe(id) ON DELETE CASCADE,
    metal VARCHAR(50),
    waga NUMERIC(10, 2) CHECK (waga >= 0)
);

CREATE TABLE faktury_sprzedazowe (
    id SERIAL PRIMARY KEY,
    nr_faktury VARCHAR(50) NOT NULL,
    data DATE NOT NULL,
    firma VARCHAR(100) NOT NULL
);

CREATE TABLE pozycje_faktury_sprzedazowe (
    id SERIAL PRIMARY KEY,
    faktura_id INTEGER NOT NULL REFERENCES faktury_sprzedazowe(id) ON DELETE CASCADE,
    metal VARCHAR(50),
    waga NUMERIC(10, 2) CHECK (waga >= 0)
);

CREATE TABLE formularze (
    id SERIAL PRIMARY KEY,
    nr_formularza VARCHAR(50) NOT NULL,
    data DATE NOT NULL
);

CREATE TABLE pozycje_formularza (
    id SERIAL PRIMARY KEY,
    formularz_id INTEGER NOT NULL REFERENCES formularze(id) ON DELETE CASCADE,
    metal VARCHAR(50),
    waga NUMERIC(10, 2) CHECK (waga >= 0)
);

CREATE VIEW liczba_formularzy AS
SELECT COUNT(*) AS liczba
FROM formularze;

CREATE VIEW liczba_odbiorcow AS
SELECT COUNT(*) AS liczba
FROM kontrahenci;

CREATE VIEW laczna_ilosc_metali AS
SELECT COUNT(*) AS liczba_metali
FROM (
    SELECT DISTINCT 
        TRIM(LOWER(
            CASE 
                WHEN metal = 'żelazo i stal' THEN 'żelazo'
                ELSE metal
            END
        )) AS metal_norm
    FROM pozycje_formularza

    UNION

    SELECT DISTINCT 
        TRIM(LOWER(
            CASE 
                WHEN metal = 'żelazo i stal' THEN 'żelazo'
                ELSE metal
            END
        )) AS metal_norm
    FROM pozycje_faktury_zakupowe
) AS unikalne_metale;

CREATE VIEW zakupy_dzienne AS
SELECT
    data,
    metal,
    SUM(waga) AS suma_zakupow
FROM (
    SELECT
        fz.data::date AS data,
        TRIM(LOWER(pfz.metal)) AS metal,
        pfz.waga
    FROM faktury_zakupowe fz
    JOIN pozycje_faktury_zakupowe pfz ON fz.id = pfz.faktura_id
    WHERE pfz.waga IS NOT NULL AND pfz.metal IS NOT NULL

    UNION ALL
    SELECT
        ff.data::date AS data,
        TRIM(LOWER(pff.metal)) AS metal,
        pff.waga
    FROM formularze ff
    JOIN pozycje_formularza pff ON ff.id = pff.formularz_id
    WHERE pff.waga IS NOT NULL AND pff.metal IS NOT NULL
) AS wszystkie
GROUP BY data, metal
ORDER BY data DESC, metal;

CREATE VIEW sprzedaz_dzienna AS
SELECT 
    fs.data,
    pfs.metal,
    SUM(pfs.waga) AS suma_sprzedazy
FROM faktury_sprzedazowe fs
JOIN pozycje_faktury_sprzedazowe pfs ON fs.id = pfs.faktura_id
GROUP BY fs.data, pfs.metal
ORDER BY fs.data DESC, pfs.metal;

CREATE VIEW stan_magazynowy AS
WITH daty AS (
    SELECT data FROM zakupy_dzienne
    UNION
    SELECT data FROM sprzedaz_dzienna
),
dni AS (
    SELECT generate_series(MIN(data), MAX(data), interval '1 day')::date AS data
    FROM daty
),
metale AS (
    SELECT DISTINCT metal FROM pozycje_faktury_zakupowe
    UNION
    SELECT DISTINCT metal FROM pozycje_faktury_sprzedazowe
),
kalendarz AS (
    SELECT d.data, m.metal
    FROM dni d CROSS JOIN metale m
),
sumy_dzienne AS (
    SELECT 
        k.data,
        k.metal,
        COALESCE(z.suma_zakupow, 0) AS zakup,
        COALESCE(s.suma_sprzedazy, 0) AS sprzedaz
    FROM kalendarz k
    LEFT JOIN zakupy_dzienne z ON z.data = k.data AND z.metal = k.metal
    LEFT JOIN sprzedaz_dzienna s ON s.data = k.data AND s.metal = k.metal
),
narastajaco AS (
    SELECT 
        data,
        metal,
        zakup,
        sprzedaz,
        SUM(zakup - sprzedaz) OVER (PARTITION BY metal ORDER BY data) AS stan_magazynowy
    FROM sumy_dzienne
)
SELECT *
FROM narastajaco
ORDER BY data DESC, metal;

CREATE VIEW stan_magazynowy_biezacy AS
SELECT *
FROM stan_magazynowy
WHERE data = (SELECT MAX(data) FROM stan_magazynowy);

CREATE VIEW calkowita_masa AS
SELECT 
    SUM(stan_magazynowy) AS suma_wag
FROM 
    stan_magazynowy_biezacy;

CREATE VIEW dzienny_raport_metali AS
SELECT
    COALESCE(z.data, s.data) AS data,
    COALESCE(z.metal, s.metal) AS metal,
    COALESCE(z.suma_zakupow::text, 'brak') AS suma_zakupow,
    COALESCE(s.suma_sprzedazy::text, 'brak') AS suma_sprzedazy,
    COALESCE((s.suma_sprzedazy - z.suma_zakupow)::text, 'brak') AS roznica_sprzedaz_zakup
FROM
    zakupy_dzienne z
FULL OUTER JOIN
    sprzedaz_dzienna s
ON
    z.data = s.data AND z.metal = s.metal
ORDER BY
    metal;

