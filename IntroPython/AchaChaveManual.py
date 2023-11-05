import pyautogui
import itertools
import time
import random
import string

# Caracteres a serem usados para gerar sequências
characters = string.ascii_letters + '.,? '

# Número máximo de caracteres na sequência

max_length = 9

# Loop principal
for length in range(3, max_length + 1):
    # GFWW
    # ere todas as combinações possíveis para o comprimento atual
    combinations = list(itertools.product(characters, repeat=length))
    
    # Embaralhe as combinações para que sejam escolhidas aleatoriamente
    random.shuffle(combinations)
    
    for i, combination in enumerate(combinations, 1):
        # Construa a sequência a partir da combinação
        sequence = ''.join(combination)
        
        # Aperte o botão esquerdo do mouse
        pyautogui.click(button='left')
        
        # Escreva a sequência
        pyautogui.typewrite(sequence)
        
        # Pressione Enter
        pyautogui.press('enter')
        
        # Espere em segundos
        time.sleep(0.0005)
        
        
        # Exiba a sequência e o número do loop no terminal
        print(f'Loop {i} - Sequência: {sequence}')
