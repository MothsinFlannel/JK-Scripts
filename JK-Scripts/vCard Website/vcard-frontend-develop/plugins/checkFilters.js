export default (context, inject) => {
  function checkFilters(filters, excludedFilters = []) {
    const filtersValues = []
    let isFiltersEmpty = true

    for (const [key, value] of Object.entries(filters)) {
      if (!excludedFilters.includes(key)) {
        filtersValues.push(value)
      }
    }

    filtersValues.forEach((filter) => {
      if (filter || filter === 0) {
        isFiltersEmpty = false
      }
    })

    return isFiltersEmpty
  }

  inject('checkFilters', checkFilters)

  context.$checkFilters = checkFilters
}
