from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import string
import itertools

# Configurar o driver do Chrome com o caminho do executável
chrome_options = webdriver.ChromeOptions()
chrome_options.binary_location = 'C:\\ProgramData\\Microsoft\\Windows\\Start Menu\\Programs\\Google Chrome.lnk'
driver = webdriver.Chrome(options=chrome_options)

# Texto criptografado
encrypted_text = "YmQeLIWWQ8rLrUJDW-cK0A"

# Caracteres permitidos
allowed_chars = string.ascii_letters + ".,? "

# Função para testar uma chave
def test_key(driver, key):
    driver.get("https://www.invertexto.com/texto-criptografado")

    text_area = driver.find_element_by_name("codigo")
    chave_input = driver.find_element_by_name("chave2")
    decode_button = driver.find_element_by_name("decode")

    text_area.clear()
    text_area.send_keys(encrypted_text)
    chave_input.clear()
    chave_input.send_keys(key)

    decode_button.click()

    # Esperar até que o elemento de resultado seja visível
    result_element = WebDriverWait(driver, 10).until(
        EC.visibility_of_element_located((By.NAME, "resultado"))
    )

    decrypted_text = result_element.text
    if decrypted_text != encrypted_text:
        print(f"Chave encontrada: {key}")
        print(f"Texto descriptografado: {decrypted_text}")
        return True
    return False

# Iniciar com sequências de 1 caractere e aumentar gradualmente
for length in range(1, 10):
    print(f"Testando sequências de {length} caracteres:")
    for sequence in itertools.product(allowed_chars, repeat=length):
        key = ''.join(sequence)
        if test_key(driver, key):
            driver.quit()
            exit()
