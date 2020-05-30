$('input[type=tel]').mask('+7 000 000-00-00', {
  placeholder: '+7 ___ ___-__-__',
});

function customer_car_form(options) {
  let operandInput = $(options.customer.input),
      carInput = $(options.car.input),
      autocompleteCarUrl = options.car.autocomplete,
      autocompleteClientsUrl = options.customer.autocomplete;

  if (!operandInput.val()) {
    $('#' + operandInput.attr('id') + ' option').text('Выберите клиента');
  }

  if (!carInput.val()) {
    $('#' + carInput.attr('id') + ' option').text('Выберите автомобиль');
  }

  let prefetchCars = function() {
    if (!operandInput.val()) {
      return;
    }

    let url = autocompleteCarUrl.replace('__CUSTOMER__', operandInput.val());

    $.ajax({
      type: 'GET',
      url: url,
    }).then(function(data) {
      const currentValue = carInput.val();
      const cars = data.results;

      if (0 === cars.length) {
        return;
      }

      let value = null;
      if ('' !== currentValue) {
        value = currentValue;
      } else if (0 < cars.length) {
        value = cars[0]['id'];
      }

      carInput.select2('destroy').select2({
        theme: 'bootstrap',
        language: 'ru',
        data: cars,
      });

      carInput.val(value).trigger('change');
    });
  };
  let prefetchClients = function() {
    if (!carInput.val()) {
      return;
    }

    let url = autocompleteClientsUrl.replace('__CAR__', carInput.val());

    $.ajax({
      type: 'GET',
      url: url,
    }).then(function(data) {
      const currentValue = operandInput.val();
      const list = data.results;

      if (0 === list.length) {
        return;
      }

      operandInput.select2('destroy').select2({
        theme: 'bootstrap',
        language: 'ru',
        data: list,
      });

      let value = null;
      if ('' !== currentValue) {
        value = currentValue;
      } else if (0 < list.length) {
        value = list[0]['id'];
      }

      operandInput.val(value).trigger('change');
    });
  };

  operandInput.on('select2:select', prefetchCars);
  carInput.on('select2:select', prefetchClients);
}
