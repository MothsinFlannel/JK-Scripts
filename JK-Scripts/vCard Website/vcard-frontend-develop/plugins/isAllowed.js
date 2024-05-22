export default (context, inject) => {
  function isAllowed(path) {
    const user = context.store.getters['user/user']

    if (user && user.permissions) {
      let result = false

      outer: for (let i = 0; i < user.permissions.length; i++) {
        const statePermission = user.permissions[i].split('/')
        const currentPermission = path.split('/')

        for (let j = 0; j < currentPermission.length; j++) {
          if (
            currentPermission[j] === statePermission[j] ||
            statePermission[j] === '*'
          ) {
            if (
              j === currentPermission.length - 1 ||
              j === statePermission.length - 1
            ) {
              result = true
              break outer
            }
          } else {
            break
          }
        }
      }

      return result
    }

    return false
  }

  inject('isAllowed', isAllowed)

  context.$isAllowed = isAllowed
}
