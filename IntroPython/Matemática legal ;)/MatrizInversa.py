matriz = [[0, 0],
          [0, 0]]

for i in range(2):
    for j in range (2):
        matriz[i][j] = float(input(f'Digite um valor na matriz A({i},{j}): '))
             
print(f'Matriz A: {matriz}')

determinante = (matriz[0][0]*matriz[1][1])-(matriz[0][1]*matriz[1][0])

print (f'Determinante da matriz A: {determinante}')

if determinante <= 0:
    print (f'A matriz A não tem inversa pois seu determinante é: {determinante}')
else:
    matrizAdjunta = [[(matriz[1][1]),(matriz[0][1]*-1)],
                     [(matriz[1][0]*-1),(matriz[0][0])]]

    print (f'Matriz Adjunta: {matrizAdjunta}')
    
    matrizInversa = ([[format((1/determinante)*(matrizAdjunta[0][0]),'.2f'),format((1/determinante)*(matrizAdjunta[0][1]),'.2f')],
                      [format((1/determinante)*(matrizAdjunta[1][0]),'.2f'),format((1/determinante)*(matrizAdjunta[1][1]),'.2f')]])
                      
    
    print (f'Matriz Inversa: {matrizInversa}')