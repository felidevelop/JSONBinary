# JSONBinary v0.1
Script para codificar objetos a JSON y encapsular contendo binario dentro de los mismos JSON

### Problematica
JSON es un formato muy poderoso y un estandar que sirve para serializar un objeto al empaquetarlo a un string transferible por la red. El problema radica en que no es posible empaquetar cualquier contenido mediante este JSON tradicional, por ejemplo, contenido binario puro de un archivo. Solucion? Convertir el contenido binario a base64, quiza? No. La base 64 va a aumentar en un 33% aproximadamente el tamaño del contenido original. No seria optmizo hacer eso.

Entonces lo que se necesita es recibir el contenido binario puro dentro del mismo JSON sin la necesidad de aumentar tanto la cantidad de bytes que se envien por la red.

### Funcionamiento
JSONBinary no tiene un funcionamiento muy complejo, es bastante simple en realidad. El truco esta en separar lo que es JSON de lo que es contenido binario. Para eso necesitamos numero indicadores que nos digan la posicion y largo de cada parte.

Primero, JSON no soporta codificar cualquier tipo de caracter, por lo que escribir el contenido binario puro dentro de una variable en un objeto no sirve, eso causaria error. Entonces debemos escribir este contenido fuera del JSON. Para eso se utilizar numeros escritos dentro del contenido para indicar en que posicion exacta comienza el contenido de un archivo y que tan largo es.

### Estructura
| Variable | Posicion | Largo | Contenido |
| :--- | :--- | :--- | :--- |
| Largo JSON | 0 | 4 bytes | Numero en formato Big endian del largo del JSON |
| JSON | 4 | Largo indicado por "Largo JSON" | Contenido del JSON |
| Archivo Binario | 4 + Largo JSON | - | Comienza el contenido binario del primer archivo |

Dentro del JSON es posible guardar el contenido binario de un archivo como tipo de objeto JSONBinary. Esta clase recibe el contenido binario puro del archivo y se encargada de tenerlo en memoria. Esto es vital para posteriormente coordinar la posiciones y largos exactos del JSON y del contenido de cada archivo.
Cada variable dentro del JSON como tipo de dato JSONBinary se coordina para convertir ese dato en la siguiente estructura:
(Este contenido es guardado como base64 porque sera condificado por json, primero debe aplicarse un base64 decode para obtener el contenido exacto)
| Variable | Posicion | Largo | Contenido |
| :--- | :--- | :--- | :--- |
| Prefijo | 0 | 8 | Prefijo indicado como "JSONBIN:" |
| Offset del archivo | 8 | 4 | Numero en formato Big endian de la posicion inicial del archivo |
| Largo del archivo | 12 | 4 | Numero en formato Big endian del largo del archivo |

Por lo tanto el proceso es. Extraer los primeros 4 bytes para saber el largo exacto del JSON. Despues extraer desde la posicion 4 hasta el largo indicado para recuperar el JSON original. Despues se recorre todas las variables del json y cada vez que se reconozca una variable con el formato "JSONBIN:[8 bytes]" codificados como base 64 que puede reconocerse como "SlNPTkJJTjowMDAwMDAwMA==" que es el resultado de pasar "JSONBIN:00000000" a base 64, comienza el proceso de extraer el contenido binario puro del archivo.

En este paso se extrae el numero big endian desde la posicion 8 con un largo de 4 bytes, y este numero indica la posicion del primer byte a la que apunta esta variable, dentro de todo el contenido binario, y con todo el contenido me refiero a (El numero inicial "Largo JSON" + JSON + El contenido de todos los archivos). Despues extraemos desde la psocion 12 con un largo de 4 bytes, y este numero indica el largo del archivo. Solo queda extraer el desde y hasta y reemplazar todo eso y pegarlo en la variable que contenido todo este puntero dentro del JSON. Haciendo este proceso por cada uno se puede recrear el JSON original y todas las variables que tienen contenido binario los recuperan como copias exactas.

Asi es posible manejar un json que contene contenido binario puro para usarlos dentro de JS o cualquier otro lenguaje a bajo nivel de programacion. No sera necesario manejar string pesados o realizar transformacion previas (segun sea el caso). Sin mencionar el ahorro de bytes transferidos por la red y ahorrar el 33% extra de peso de una transformacion a base64. Segun este analisis, el peso de este json solo es de la suma de todos los archivo adjuntos + 4 + el largo del JSON.

### Futuras mejoras
Esta proyectado, o pueden servir de inspiracion para ti, implementar esta logica a mas lenguajes. Ya sean Python o C# o C++, entre otros que se usen mucho para recibir contenido por red y manejar una lista de archivos pesados en una sola consulta.

### Licencia
Este proyecto está bajo la **Licencia MIT**. 

En palabras simples: 
* **Puedes:** Usar, copiar, modificar y vender este código de forma gratuita.
* **Condición:** Debes incluir el aviso de copyright y la licencia original en cualquier copia que hagas.
* **Garantía:** El software se entrega "tal cual", sin garantías de ningún tipo (el autor no es responsable de problemas).
