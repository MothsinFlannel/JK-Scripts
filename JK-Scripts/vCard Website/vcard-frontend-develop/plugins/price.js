export default (context, inject) => {
  function getFormattedPrice(value) {
    if (typeof value === 'number') {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(value)
    }

    return null
  }

  inject('getFormattedPrice', getFormattedPrice)

  context.$getFormattedPrice = getFormattedPrice
}
