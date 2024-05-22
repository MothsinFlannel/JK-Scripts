export default (context, inject) => {
  function getErrorData(error) {
    console.error(error)

    if (
      error &&
      error.response &&
      error.response.data &&
      error.response.data.exception &&
      typeof error.response.data.exception.data === 'object'
    ) {
      return error.response.data.exception.data
    }

    return {}
  }

  inject('getErrorData', getErrorData)

  context.$getErrorData = getErrorData
}
