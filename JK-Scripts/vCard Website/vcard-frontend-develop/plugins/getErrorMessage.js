export default (context, inject) => {
  function getErrorMessage(error) {
    console.error(error)

    if (
      error &&
      error.response &&
      error.response.data &&
      error.response.data.exception &&
      typeof error.response.data.exception.message === 'string'
    ) {
      return error.response.data.exception.message
    }

    if (error instanceof Error) {
      return error.toString()
    }

    if (typeof error === 'string') {
      return error
    }

    return 'Something went wrong'
  }

  inject('getErrorMessage', getErrorMessage)

  context.$getErrorMessage = getErrorMessage
}
