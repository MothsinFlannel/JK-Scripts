module.exports = (shipit) => {
  require('shipit-deploy')(shipit)

  shipit.initConfig({
    default: {
      deleteOnRollback: false,
      deployTo: '~/vcard-frontend',
      keepReleases: 3,
      key: '~/.ssh/id_rsa',
      repositoryUrl: 'git@gitlab.com:voodoo-rocks/vcard-frontend.git',
      shallowClone: true,
    },
    development: {
      branch: 'develop',
      servers: 'ubuntu@voodoo.work',
    },
    staging: {
      branch: 'develop',
      servers: 'ubuntu@voodoo.services',
    },
    production: {
      branch: 'master',
      servers: 'ubuntu@app.vcardsys.com',
    },
  })

  shipit.on('updated', () => {
    shipit.start('install-dependencies')
  })

  shipit.blTask('install-dependencies', async () => {
    await shipit.remote(
      `cd ${shipit.releasesPath}/${shipit.releaseDirname} && npm ci`
    )

    shipit.emit('dependencies-installed')
  })

  shipit.on('dependencies-installed', () => {
    shipit.start('build-application')
  })

  shipit.blTask('build-application', async () => {
    const env = shipit.environment || 'development'

    await shipit.remote(
      `cd ${shipit.releasesPath}/${shipit.releaseDirname} && npm run build:${env}`
    )

    shipit.emit('application-built')
  })

  shipit.on('application-built', () => {
    shipit.start('clear-directory')
  })

  shipit.blTask('clear-directory', async () => {
    await shipit.remote(
      `cd ${shipit.releasesPath}/${shipit.releaseDirname} && rm -rf assets components deploy .editorconfig .eslintrc.js .git .gitignore .gitlab-ci.yml lang layouts node_modules .nuxt nuxt.config.js package.json package-lock.json pages plugins .prettierrc README.md static store utils`
    )
  })

  shipit.on('deployed', function () {
    shipit.start('create-symlink')
  })

  shipit.blTask('create-symlink', async () => {
    await shipit.remote(
      `ln -fs ~/vcard-frontend/current/dist /var/www/vcard-frontend`
    )

    if (shipit.environment === 'production') {
      await shipit.remote(
        `ln -s ~/vcard-backend/current/web /var/www/vcard-frontend/api`
      )
    }
  })
}
