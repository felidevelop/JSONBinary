# JSONBinary
Script para codificar objetos a JSON y encapsular contendo binario dentro de los mismos JSON

### Problematica
JSON es un formato muy poderoso y un estandar que sirve para serializar un objeto al empaquetarlo a un string transferible por la red. El problema radica en que no es posible empaquetar cualquier contenido mediante este JSON tradicional, por ejemplo, contenido binario puro de un archivo. Solucion? Convertir el contenido binario a base64, quiza? No. La base 64 va a aumentar en un 33% aproximadamente el tamaño del contenido original. No seria optmizo hacer eso.

Entonces lo que se necesita es recibir el contenido binario puro dentro del mismo JSON sin la necesidad de aumentar tanto la cantidad de bytes que se envien por la red.

### Funcionamiento
JSONBinary no tiene un funcionamiento muy complejo, es bastante simple en realidad. El truco esta en separar lo que es JSON de lo que es contenido binario. Para eso necesitamos numero indicadores que nos digan la posicion y largo de cada parte.

Primero, JSON no soporta codificar cualquier tipo de caracter, por lo que escribir el contenido binario puro dentro de una variable en un objeto no sirve, eso causaria error. Entonces debemos escribir este contenido fuera del JSON. Para eso se utilizar numeros escritos dentro del contenido para indicar en que posicion exacta comienza del contenido de un archivo y que tan largo es.

### Estructura
| Posicion | Largo | Contenido |
| :--- | :--- | :--- |
| 0 | 4 bytes | Numero en formato Big endian del largo del JSON |
