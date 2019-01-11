
var special = ['À','à','Á','á','Â','â','Ã','ã','Ä','ä','Å','å','Ă','ă','Ą','ą','Ć'
               , 'ć','Č','č','Ç','ç','Ď','ď','Đ','đ', 'È','è','É','é','Ê','ê','Ë','ë'
               , 'Ě','ě','Ę','ę','Ğ','ğ','Ì','ì','Í','í','Î','î','Ï','ï', 'Ĺ','ĺ','Ľ'
               , 'ľ','Ł','ł','Ñ','ñ','Ň','ň','Ń','ń','Ò','ò','Ó','ó','Ô','ô','Õ','õ'
               , 'Ö','ö','Ø','ø','ő','Ř','ř','Ŕ','ŕ','Š','š','Ş','ş','Ś','ś','Ť','ť'
               , 'Ť','ť','Ţ','ţ','Ù','ù','Ú','ú','Û','û','Ü','ü','Ů','ů','Ÿ','ÿ','ý'
               , 'Ý','Ž','ž','Ź','ź','Ż','ż','Þ','þ','Ð','ð','ß','Œ','œ','Æ','æ','µ']

, standard = ['A','a','A','a','A','a','A','a','Ae','ae','A','a','A','a','A','a'
              , 'C','c','C','c','C','c','D','d','D','d', 'E','e','E','e','E','e'
              , 'E','e','E','e','E','e','G','g','I','i','I','i','I','i','I','i','L'
              , 'l','L','l','L','l','N','n','N','n','N','n', 'O','o','O','o','O'
              , 'o','O','o','Oe','oe','O','o','o','R','r','R','r','S','s','S','s'
              , 'S','s','T','t','T','t','T','t','U','u','U','u','U','u','Ue','ue'
              , 'U','u','Y','y','Y','y','Z','z','Z','z','Z','z','TH','th','DH','dh'
              , 'ss','OE','oe','AE','ae','u'];

function standarize(text){
    for (var i = 0, l = special.length; i < l; i++){
        if (typeof special[i] == 'string') special[i] = new RegExp(special[i], 'g');
        text = text.replace(special[i], standard[i]);
    }
    return text;
};

function namize(str){
    if (!str) return
    return str
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '_') // Replace spaces with -
        .replace(/[^\w\-]+/g, '') // Remove all non-word chars
        .replace(/\-\-+/g, '_') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
};

function slugize(str){
    return standarize(namize(str)).toLowerCase()
        .replace(/ +/g, '-')
        .replace(/[^-\w]/g, '');
};

// On load
$('.slug').val(slugize($('.slugglable').val()));

// On key press
$('.slugglable').keypress(function(){
    $('.slug').val(slugize($('.slugglable').val()));
});

// On focus
$('.slug').focus(function(){
    $('.slug').val(slugize($('.slugglable').val()));
});