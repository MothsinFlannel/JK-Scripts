import _ from 'lodash'

export default (context, inject) => {
  function cityStateZip(location) {
    return [
      location.city,
      [_.upperCase(location.state), location.zipCode]
        .filter((item) => item !== null)
        .join(' '),
      location.county ? location.county + ' County' : null,
    ]
      .filter((item) => item !== null)
      .join(', ')
  }

  inject('cityStateZip', cityStateZip)
  context.$cityStateZip = cityStateZip
}
