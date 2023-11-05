import base64
from Crypto.Cipher import AES

# Texto criptografado e chave conhecida
encrypted_text = "5lK-dtZg-W38O3DoalcmXA"
known_key = "put"

# Caracteres permitidos na chave
charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz,.? "

def decrypt_aes(text, key):
    cipher = AES.new(key.encode('utf-8'), AES.MODE_ECB)
    decrypted_text = cipher.decrypt(base64.b64decode(text)).decode('utf-8', 'ignore')
    return decrypted_text

def generate_combinations(charset, length):
    from itertools import product
    for combo in product(charset, repeat=length):
        yield ''.join(combo)

max_key_length = 16  # Tamanho máximo da chave

for key_length in range(1, max_key_length + 1):
    print(f"Testando chaves de {key_length} caracteres:")
    
    for iteration, key in enumerate(generate_combinations(charset, key_length)):
        key_padded = key.ljust(16, '\x00')  # Preencher com '\x00' para atingir 16 bytes
        
        print(f"Loop {iteration + 1}: Tentando chave {key}")
        
        decrypted = decrypt_aes(encrypted_text, key_padded)
        
        if decrypted.startswith('\x00'):
            print("Chave correta encontrada:", key)
            print("Texto descriptografado:", decrypted.strip('\x00'))
            break
