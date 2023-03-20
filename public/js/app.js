var fruitTable, favoriteTable;
$(document).ready(function () {
    fruitTable = $('#list').DataTable({
        ajax: '/api/list',
         columns: [
            { data: 'name' },
            { data: 'genus' },
            { data: 'family' },
            { data: 'forder' },
            { data: 'nutritions.carbohydrates' },
            { data: 'nutritions.protein' },
            { data: 'nutritions.fat' },
            { data: 'nutritions.calories' },
            { data: 'nutritions.sugar' },
            {
              data: null,
              render: function ( data, type, row ) {
                if (!data.favorite) {
                    return '<button onclick=onClickFavorite(' + 'true,' + data.id + ')>' + 'Add To Favorite List' + '</button>'
                } else {
                    return '<button onclick=onClickFavorite(' + 'false,' + data.id + ')>' + 'Remove From Favorite List' + '</button>'
                }
              }
            }
        ],
    });
    favoriteTable = $('#favorites').DataTable({
        ajax: {
        	url: '/api/list/favorites',
        	dataSrc: function ( json ) {
                const totalFats = json.data.reduce((accumulator, object) => {
					 return accumulator + parseFloat(object.nutritions.fat);
				}, 0);
				$('#fats').text(totalFats);
                return json.data;
            }   
        },
        columns: [
            { data: 'name' },
            { data: 'genus' },
            { data: 'family' },
            { data: 'forder' },
            { data: 'nutritions.carbohydrates' },
            { data: 'nutritions.protein' },
            { data: 'nutritions.fat' },
            { data: 'nutritions.calories' },
            { data: 'nutritions.sugar' },
            {
              data: null,
              render: function ( data, type, row ) {
                return '<button onclick=onClickFavorite(' + 'false,' + data.id + ')>' + 'Remove From Favorite List' + '</button>'
              }
            }
        ]
    });
});
function onClickFavorite(type, id) {
    $.ajax({
      type: "POST",
      url: '/api/favorite',
      data: {id, type: (type == true ? 1 : 0)},
      success: function(res) {
        if (res) {
            alert(res.message);
        	fruitTable.ajax.reload();
        	favoriteTable.ajax.reload();
        }
      },
      dataType: 'json'
    });
}