# This exercise is for the WIFI Web Developer course.

**TODO:**

 Erstelle eine Datenbank Tabelle in der die Newsletter Anmeldungen gespeichert werden. Ausserdem sollen alle Newsletter Anmeldungen angezeigt werden. Es soll auch die Möglichkeit geschaffen werden, die Newsletter Anmeldungen zu ändern und aus der DB Tabelle zu löschen.
- Die E-Mail Adresse soll in der DB Tabelle eindeutig sein.

- Das Formular sollte inhaltlich wie folgt überprüft werden: 
    - Anrede: Pflichtfeld
    - Vorname: Pflichtfeld, mindestens 2 Zeichen, maximal 50 Zeichen
    - Nachname: Pflichtfeld, mindestens 2 Zeichen, maximal 50 Zeichen
    - E-Mail: Pflichtfeld, E-Mail
    - Datenschutz: Pflichtfeld

- Optional könnte eine Paginierung (max. 10 Anmeldungen pro Seite) und eine Suche nach E-Mail Adressen erstellt werden.

This exersice is similar to the other Repo - newsletter_CSV

Practicing the following in PHP:
- using SQL statments from class methods
- deeper understanding of interfaces, classes, etc

**Requirements and configurations**
- composer is required to install certain packages
- check and configurate to your database connection in `/config/database.php`
