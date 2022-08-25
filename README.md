# Instalacja
1) Tworzymy folder app/code/Usercom/Analytics/
```code
mkdir -p app/code/Usercom/Analytics/
```
2) Przechodzimy do folderu app/code/Usercom/Analytics
```code
cd app/code/Usercom/Analytics/
```
3) Pobieramy wtyczke
```code
git clone https://github.com/UserEngage/User.com-Magento2-plugin.git .
```
4) Przechodzimy do głownego folderu Magento
```code
cd ../../../../
```
5) Aktualizujemy konfigurację Magento
```code
 bin/magento s:up && bin/magento s:d:c && bin/magento s:sta:d -f && bin/magento c:c && bin/magento c:f
 ```
 # Funkcjonalności
1. Instalacja kodu śledzącego widgetu na wszystkich podstronach sklepu.
2. Zbieranie danych o użytkownikach.
3. Przesylanie danych o zdarzeniach.
4. Synchronizacja danych historycznych,
5. Synchronizacja newsletter.

# Synchronizacja newsletter
1. Na stronie https://your-company.user.com/ tworzymy nowe Automations
2. Po zmianie atrubutu "Unsubscribed from emails" wyłowujemy API request do "http://your-domain/rest/all/V1/usercom-analytics/newsletter". Dodajemy customowy header "Content-Type", który ma zawartość "application/json"

# Dla developerów
- custom id - indetyfikator nadany w Magento
- usercom id - indetyfikator nadany w user.com
- userKey - indetyfikator urzytkownika stworzony widget-em user.com
