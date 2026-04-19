function JSONBinaryDecode(jsonbinary){
   const view = new DataView(jsonbinary);
   const decoder = new TextDecoder();
   const jsonLen = view.getUint32(0, false);

   const json = JSON.parse(decoder.decode(new Uint8Array(jsonbinary, 4, jsonLen)));

   function unpackData(data, json){
      for (let key in json){
         let valor = json[key];

         if (typeof valor === 'string' && valor.startsWith("SlNPTkJJT")){ // base64 de "JSONBIN:" sin importar el offset y el len
            valor = atob(valor);
            const confirm = valor.startsWith("JSONBIN:");

            if (confirm){
               const buffer = Uint8Array.from(valor, c => c.charCodeAt(0)).buffer;
               const offset = new DataView(buffer).getUint32(8, false);
               const len = new DataView(buffer).getUint32(12, false);

               json[key] = new Uint8Array(data.slice(offset, offset + len));
            }
            
         }else if (valor !== null && typeof valor === 'object'){
            unpackData(data, valor);
         }
      }
   }

   unpackData(jsonbinary, json);

   return json;
}