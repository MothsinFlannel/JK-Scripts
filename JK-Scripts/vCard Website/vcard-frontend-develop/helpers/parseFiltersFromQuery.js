export default function (query, parameters) {
  const result = {}

  for (const parameter of parameters) {
    const parameterName =
      typeof parameter === 'string' ? parameter : parameter.name
    const value = query[parameterName]

    if (
      value === null ||
      value === undefined ||
      (typeof value === 'string' && value.length === 0)
    ) {
      result[parameterName] =
        typeof parameter === 'string' ? null : parameter.default || null
    } else if (value === 'true') {
      result[parameterName] = true
    } else if (value === 'false') {
      result[parameterName] = false
    } else if (value === `${+value}`) {
      result[parameterName] = +value
    } else {
      result[parameterName] = value
    }
  }

  return result
}
