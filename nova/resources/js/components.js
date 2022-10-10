import camelCase from 'lodash/camelCase'
import upperFirst from 'lodash/upperFirst'
import CustomError404 from '@/views/CustomError404'
import CustomError403 from '@/views/CustomError403'
import CustomAppError from '@/views/CustomAppError'
import ResourceIndex from '@/views/Index'
import ResourceDetail from '@/views/Detail'
import Attach from '@/views/Attach'
import UpdateAttached from '@/views/UpdateAttached'
// import Lens from '@/views/Lens'

export function registerViews(app) {
  // Manually register some views...
  app.component('CustomError403', CustomError403)
  app.component('CustomError404', CustomError404)
  app.component('CustomAppError', CustomAppError)
  app.component('ResourceIndex', ResourceIndex)
  app.component('ResourceDetail', ResourceDetail)
  app.component('AttachResource', Attach)
  app.component('UpdateAttachedResource', UpdateAttached)
  // app.component('Lens', Lens)

  const requireComponent = require.context(
    './components',
    true,
    /[A-Z]\w+\.(vue)$/
  )

  requireComponent.keys().forEach(fileName => {
    const componentConfig = requireComponent(fileName)

    const componentName = upperFirst(
      camelCase(
        fileName
          .split('/')
          .pop()
          .replace(/\.\w+$/, '')
      )
    )

    app.component(componentName, componentConfig.default || componentConfig)
  })
}
