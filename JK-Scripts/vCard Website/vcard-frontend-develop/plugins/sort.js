export default (context, inject) => {
  function getSortValue(value) {
    const sortBy = value.startsWith('-') ? value.slice(1) : value
    const sortDesc = value.startsWith('-') ? 'desc' : 'asc'

    return `${sortBy
      .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
      .toLowerCase()}+${sortDesc}`
  }

  inject('getSortValue', getSortValue)

  context.$getSortValue = getSortValue
}
