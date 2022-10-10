import camelCase from 'lodash/camelCase'
import upperFirst from 'lodash/upperFirst'

function registerComponents(app, type, requireComponent) {
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

    app.component(
      type + componentName,
      componentConfig.default || componentConfig
    )
  })
}

export function registerFields(app) {
  registerComponents(
    app,
    'Index',
    require.context(`./fields/Index`, true, /[A-Z]\w+\.(vue)$/)
  )
  registerComponents(
    app,
    'Detail',
    require.context(`./fields/Detail`, true, /[A-Z]\w+\.(vue)$/)
  )
  registerComponents(
    app,
    'Form',
    require.context(`./fields/Form`, true, /[A-Z]\w+\.(vue)$/)
  )
  registerComponents(
    app,
    'Filter',
    require.context(`./fields/Filter`, true, /[A-Z]\w+\.(vue)$/)
  )
}
