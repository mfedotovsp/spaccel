//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

$(body).on('click', '.changeAccessWishList', function(){
    $('.block_wishListChangeAccess').toggle('display');
    $('.block_wishListChangeAccessForm').toggle('display');
});

$(body).on('click', '.cancelChangeAccessWishList', function(){
    $('.block_wishListChangeAccess').toggle('display');
    $('.block_wishListChangeAccessForm').toggle('display');
});