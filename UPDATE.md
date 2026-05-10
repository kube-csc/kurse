## Update Anleitung
**Version V00.02.02**

***Neue Funktionen***
Einbetten des Kursangebot ins fremden Webseiten über iFrame. oder  API-Kursangebot.
Der Code zum einbetten in einer fremden Webseite kann im Dashboard erstellt werden.
(Beispiele sind im Ordner Beispiele zu finden)


**Version V00.02.01**
Es wird https://github.com/kube-csc/vereinsverwaltung  GitHub Projekt Vereinsverwaltung ab V00.10.03 benötigt

***Neue Funktionen***
- **Fahrtenbuch erweitert:** Dashboard-Einstiege, Distanzpflege sowie Filter- und Navigationslogik wurden umgesetzt.
- **Aktivitätsbericht erweitert:** Dashboard-Einstieg, Trainer-Fahrleistungsaggregation und Jahresstatistik wurden umgesetzt.
- ICS-Downloadfunktion für Kurstermine.
- Umstellung von Sportgeräten mit mehreren Plätzen.

**Was ist zu tun?**
- `composer update`
- `php artisan migrate`

---

**Version V00.02.00**

Es wird https://github.com/kube-csc/vereinsverwaltung  GitHub Projekt Vereinsverwaltung ab V00.10.02 benötigt
composer update

***Neue Funktionen***
- FAQ-Bereich hinzugefügt.
- Header-Bild für Veranstaltungen kann nun hochgeladen werden.
- Anlegen von Kursen, Terminen, Trainings, Schnupperkursen und Fahrten.
- Trainingszeiten für Abteilungen und Mannschaften werden über einen Cronjob basierend auf dem erstellten Trainingsplan angelegt.

**Was ist zu tun?**
- `php artisan migrate`

---

**Version V00.01.02**

**Version V00.01.01**

**Version V00.01.00**
