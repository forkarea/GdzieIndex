CREATE TABLE miejsce (
  idMiejsce INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nazwaMiejsce VARCHAR(64) NOT NULL,
  PRIMARY KEY(idMiejsce)
);

CREATE TABLE grupyUzytkownik (
  idGrupyUzytkownik INTEGER(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  nazwaGrupa VARCHAR(32) NOT NULL,
  PRIMARY KEY(idGrupyUzytkownik)
);

CREATE TABLE uczelnia (
  idUczelnia INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nazwaUczelnia VARCHAR(128) NOT NULL,
  adres VARCHAR(255) NULL,
  PRIMARY KEY(idUczelnia)
);

CREATE TABLE wydzial (
  idWydzial INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idUczelnia INTEGER UNSIGNED NOT NULL,
  nazwaWydzial VARCHAR(64) NOT NULL,
  adres VARCHAR(255) NULL,
  PRIMARY KEY(idWydzial),
  FOREIGN KEY(idUczelnia)
    REFERENCES uczelnia(idUczelnia)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE kierunek (
  idKierunek INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idWydzial INTEGER UNSIGNED NOT NULL,
  nazwaKierunek VARCHAR(64) NOT NULL,
  dataRozpoczecia DATE NOT NULL,
  dataZakonczenia DATE NULL,
  opis VARCHAR(255) NULL,
  PRIMARY KEY(idKierunek),
  FOREIGN KEY(idWydzial)
    REFERENCES wydzial(idWydzial)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE grupyKierunek (
  idGrupyKierunek INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idKierunek INTEGER UNSIGNED NOT NULL,
  nazwaGrupa VARCHAR(64) NOT NULL,
  status_2 BOOL NULL,
  PRIMARY KEY(idGrupyKierunek),
  FOREIGN KEY(idKierunek)
    REFERENCES kierunek(idKierunek)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE uzytkownik (
  idUzytkownik INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idGrupyUzytkownik INTEGER(6) UNSIGNED NOT NULL,
  idGrupyKierunek INTEGER UNSIGNED NULL,
  login VARCHAR(32) NOT NULL,
  haslo VARCHAR(64) NOT NULL,
  mail VARCHAR(32) NOT NULL,
  avatar VARCHAR(32) NULL,
  logowanie DATETIME NOT NULL,
  rejestracja DATETIME NOT NULL,
  status_2 BOOL NOT NULL,
  potwierdzenie BOOL NOT NULL,
  PRIMARY KEY(idUzytkownik),
  FOREIGN KEY(idGrupyUzytkownik)
    REFERENCES grupyUzytkownik(idGrupyUzytkownik)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(idGrupyKierunek)
    REFERENCES grupykierunek(idGrupyKierunek)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE pracownik (
  idPracownik INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idUzytkownik INTEGER UNSIGNED NOT NULL,
  idWydzial INTEGER UNSIGNED NULL,
  nrPokoju VARCHAR(10) NULL,
  PRIMARY KEY(idPracownik),
  FOREIGN KEY(idWydzial)
    REFERENCES wydzial(idWydzial)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(idUzytkownik)
    REFERENCES uzytkownik(idUzytkownik)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE uzytkownikInfo (
  idUzytkownik INTEGER UNSIGNED NOT NULL,
  imie VARCHAR(64) NULL,
  nazwisko VARCHAR(64) NULL,
  plec BOOL NULL,
  dataUrodzenia DATE NULL,
  zainteresowania VARCHAR(255) NULL,
  lokalizacja VARCHAR(255) NULL,
  telefon VARCHAR(11) NULL,
  PRIMARY KEY(idUzytkownik),
  FOREIGN KEY(idUzytkownik)
    REFERENCES uzytkownik(idUzytkownik)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE indeks (
  idIndeks INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idKierunek INTEGER UNSIGNED NOT NULL,
  idUzytkownik INTEGER UNSIGNED NOT NULL,
  nrIndeks VARCHAR(10) NULL,
  utworzenie DATE NOT NULL,
  status_2 BOOL NOT NULL,
  zakonczenie DATE NULL,
  PRIMARY KEY(idIndeks),
  FOREIGN KEY(idUzytkownik)
    REFERENCES uzytkownik(idUzytkownik)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(idKierunek)
    REFERENCES kierunek(idKierunek)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE rejestratorZdarzenia (
  idRejestratorZdarzenia INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  idIndeks INTEGER UNSIGNED NOT NULL,
  idMiejsce INTEGER UNSIGNED NOT NULL,
  idUzytkownik INTEGER UNSIGNED NOT NULL,
  dataOtrzymania DATE NOT NULL,
  dataZwrotu DATE NULL,
  stasus BOOL NOT NULL,
  opis VARCHAR(255) NULL,
  PRIMARY KEY(idRejestratorZdarzenia),
  FOREIGN KEY(idMiejsce)
    REFERENCES miejsce(idMiejsce)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(idIndeks)
    REFERENCES indeks(idIndeks)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(idUzytkownik)
    REFERENCES uzytkownik(idUzytkownik)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

