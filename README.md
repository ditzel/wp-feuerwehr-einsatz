# WP Feuerwehr Einsatz

Ein benutzerdefiniertes WordPress Plugin zum Erfassen und Anzeigen von Feuerwehr-Einsätzen über Gutenberg-Blöcke.

## 🚒 Features

- **Gutenberg Block Integration**: Native WordPress Block Editor Unterstützung für einfache Verwaltung
- **Einsatzerfassung**: Erfassen Sie Feuerwehr-Einsätze mit allen relevanten Details
- **Flexible Anzeige**: Dynamische Darstellung von Einsätzen auf Ihrer Website
- **Responsive Design**: Optimiert für Desktop, Tablet und Mobile
- **Benutzerfreundliche Interface**: Einfache Bedienung auch für nicht-technische Benutzer

## 📋 Anforderungen

- WordPress 5.0 oder höher
- PHP 7.4 oder höher
- Gutenberg Block Editor aktiviert

## 🚀 Installation

### Über GitHub

1. Navigieren Sie zum Verzeichnis `/wp-content/plugins/` auf Ihrem Server
2. Klonen Sie das Repository:
   ```bash
   git clone https://github.com/ditzel/wp-feuerwehr-einsatz.git
   ```
3. Aktivieren Sie das Plugin im WordPress-Dashboard unter **Plugins**

### Manuelle Installation

1. Laden Sie das Plugin als ZIP-Datei herunter
2. Entpacken Sie die Datei in das Verzeichnis `/wp-content/plugins/`
3. Aktivieren Sie das Plugin im WordPress-Dashboard

## 📖 Verwendung

### Einsatzblock hinzufügen

1. Öffnen Sie den Block Editor auf einer Seite oder in einem Beitrag
2. Suchen Sie nach dem Block "Feuerwehr Einsatz"
3. Fügen Sie den Block hinzu und füllen Sie die erforderlichen Felder aus:
   - Einsatznummer
   - Datum und Uhrzeit
   - Einsatzart
   - Ort/Adresse
   - Beschreibung
   - Weitere Details

4. Passen Sie das Design nach Bedarf an
5. Veröffentlichen Sie den Beitrag

## 🛠️ Entwicklung

Das Plugin besteht aus:

- **PHP (64.4%)**: WordPress Plugin-Logik und Gutenberg Block Backend
- **JavaScript (23.7%)**: Block Editor Frontend und Interaktivität
- **CSS (11.9%)**: Styling und Responsive Design

### Projektstruktur

```
wp-feuerwehr-einsatz/
├── wp-feuerwehr-einsatz.php    # Main Plugin File
├── includes/                     # PHP Klassen und Funktionen
├── blocks/                       # Gutenberg Block Komponenten
├── assets/
│   ├── css/                     # Stylesheets
│   ├── js/                      # JavaScript Dateien
│   └── img/                     # Bilder
├── languages/                    # Übersetzungsdateien
└── README.md                    # Diese Datei
```

### Lokale Entwicklung

Voraussetzungen:
- Node.js 14+ und npm
- Composer (optional, für PHP Abhängigkeiten)

Setup:

```bash
# Repository klonen
git clone https://github.com/ditzel/wp-feuerwehr-einsatz.git
cd wp-feuerwehr-einsatz

# Abhängigkeiten installieren
npm install

# Entwicklungs-Build starten
npm run start

# Produktions-Build erstellen
npm run build
```

## 🌐 Internationalisierung

Das Plugin unterstützt mehrsprachige Websites. Übersetzungsdateien befinden sich im `/languages`-Verzeichnis.

Unterstützte Sprachen:
- Deutsch (de_DE)
- Englisch (en_US)

## 📝 Lizenz

Bitte überprüfen Sie die LICENSE-Datei für weitere Informationen.

## 🤝 Beitragen

Beiträge sind willkommen! Bitte erstellen Sie einen Fork des Repositories und reichen Sie einen Pull Request ein.

## 📧 Support

Bei Fragen oder Problemen öffnen Sie bitte einen Issue im [GitHub Repository](https://github.com/ditzel/wp-feuerwehr-einsatz/issues).

## 🔗 Links

- [WordPress Plugin Directory](https://wordpress.org/plugins/)
- [Gutenberg Block Editor Dokumentation](https://wordpress.org/support/article/wordpress-editor/)
- [WordPress Plugin Development Handbook](https://developer.wordpress.org/plugins/)

---

**Entwickelt mit ❤️ für die Feuerwehr-Community**