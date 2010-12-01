function str_split (string, split_length) {
    // Convert a string to an array. If split_length is specified, break the string down into chunks each split_length characters long.  
    // 
    // version: 1009.2513
    // discuss at: http://phpjs.org/functions/str_split
    // +     original by: Martijn Wieringa
    // +     improved by: Brett Zamir (http://brett-zamir.me)
    // +     bugfixed by: Onno Marsman
    // +      revised by: Theriault
    // +        input by: Bjorn Roesbeke (http://www.bjornroesbeke.be/)
    // +      revised by: Rafał Kukawski (http://blog.kukawski.pl/)
    // *       example 1: str_split('Hello Friend', 3);
    // *       returns 1: ['Hel', 'lo ', 'Fri', 'end']
    if (split_length === null) {
        split_length = 1;
    }
    if (string === null || split_length < 1) {
        return false;
    }
    string += '';
    var chunks = [], pos = 0, len = string.length;
    while (pos < len) {
        chunks.push(string.slice(pos, pos += split_length));
    }
        
    return chunks;
}

var FNV1a32 = (function() {
   return {
      fnv: function(buf, offset, len) {
        var seed = 2166136261;
         for (var i = offset; i < offset + len; i++) {
            seed ^= buf[i];
            seed += (seed << 1) + (seed << 4) + (seed << 7) + (seed << 8) + (seed << 24);
         }
         return seed;
      },
      
      getHash: function(hash) {
         return Number(hash & 0x00000000ffffffff).toString(16);
      },

      INIT: 0x811c9dc5
   };
})();