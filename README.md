# BookstackOSTicket
Script soll Vorschläge von Anleitungen anzeigen, die in Bookstack liegen.

## Funktionen
- Sucht in bestimmten Büchern (werden in der bookstack-search.php definiert) nach Übereinstimmungen
- Zeigt Ergebnisse unter dem Summary-Feld an inkl. eines kurzen Textauszugs
- Generiet einen Link, mit dem der Ticketsteller direkt zur Anleitung kommt (Login im Wiki erforderlich)

## Konfiguration
In Bookstack muss im Profil eines Users ein Token erzeugt werden. Der User muss natürlich auf die zu durchsuchenden Bücher berechtigt sein. Darüber hinaus können noch zu durchsuchende Bücher deifniert werden in der bookstack-search.php