TODO App
Proje Açıklaması ve Özellikler
TODO App, kullanıcıların günlük görevlerini yönetmelerine olanak tanıyan basit ve kullanıcı dostu bir web uygulamasıdır. Kullanıcılar görev ekleyebilir, düzenleyebilir, tamamlandı olarak işaretleyebilir ve silebilir. Uygulama, modern bir arayüz ve güvenli bir back-end ile hızlı ve etkili bir kullanıcı deneyimi sunar.
Özellikler

Görev ekleme, düzenleme, silme ve listeleme
Görevlerin tamamlanma durumunu işaretleme
Kullanıcı dostu ve duyarlı (responsive) arayüz
Görevlerin kalıcı olarak saklanması (MySQL ile)
RESTful API üzerinden veri yönetimi
Bonus: Görev kategorilendirme (örneğin, iş, kişisel)

Teknoloji Stack’i
Front-end

React: Dinamik ve bileşen tabanlı arayüz geliştirme
Tailwind CSS: Hızlı ve özelleştirilebilir stil oluşturma
Axios: API istekleri için HTTP istemcisi
React Router: Sayfalar arası gezinme için

Back-end

Node.js: Sunucu tarafı JavaScript çalıştırma ortamı
Express: Hızlı ve minimalist web framework’ü


Diğer Araçlar

NPM: Bağımlılık yönetimi
ESLint: Kod kalitesi için
Prettier: Kod formatlama

Kurulum Adımları
Ön Koşullar

Node.js (v16 veya üstü)
MongoDB (yerel veya MongoDB Atlas)
Git
Bir kod editörü (örneğin, VS Code)

Back-end Kurulumu

Proje dizinine gidin:cd todo-app/backend


Bağımlılıkları yükleyin:npm install


.env dosyasını oluşturun ve  değişkenleri ekleyin


Front-end Kurulumu

Front-end dizinine gidin:cd todo-app/frontend


Bağımlılıkları yükleyin:npm install


.env dosyasını oluşturun ve back-end API URL’sini ekleyin:REACT_APP_API_URL=http://localhost:8000/api



Çalıştırma Talimatları

Back-end’i başlatın:cd backend
npm start

Sunucu varsayılan olarak http://localhost:8000 adresinde çalışır.
Front-end’i başlatın:cd frontend
npm start

Uygulama varsayılan olarak http://localhost:3000 adresinde açılır.
Tarayıcınızda http://localhost:3000 adresine gidin ve uygulamayı kullanmaya başlayın.

API Dokümantasyonu
API, görevlerin yönetimi için RESTful bir arayüz sağlar. Ayrıntılı dokümantasyon için API Dokümantasyon Sayfası dosyasını inceleyin. Örnek endpoint’ler:

GET /api/todos: Tüm görevleri listele
POST /api/todos: Yeni görev ekle{
  "title": "Alışveriş yap",
  "category": "Kişisel",
  "completed": false
}


PUT /api/todos/:id: Görevi güncelle
DELETE /api/todos/:id: Görevi sil

Örnek Kullanım Senaryoları

Görev Ekleme:
Ana sayfada "Yeni Görev Ekle" formuna görev başlığını ve kategorisini girin.
"Ekle" butonuna tıklayın, görev listeye eklenir.


Görev Tamamlama:
Görev listesindeki bir görevin yanındaki kutucuğu işaretleyin.
Görev "Tamamlandı" olarak işaretlenir ve arayüzde vurgulanır.


Görev Silme:
Görev listesindeki bir görevin yanındaki "Sil" butonuna tıklayın.
Görev listeden kaldırılır.



Bonus Olarak Eklenen Özellikler

Kategori Filtreleme: Görevleri kategoriye göre filtreleme (örneğin, sadece "İş" görevlerini göster).
Responsive Tasarım: Mobil ve masaüstü cihazlarda sorunsuz çalışma.
Hata Yönetimi: API veya kullanıcı hatalarında bilgilendirici mesajlar.
Veri Kalıcılığı: MySQL ile görevlerin kalıcı olarak saklanması.

