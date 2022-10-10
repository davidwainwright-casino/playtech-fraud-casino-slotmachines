<script>
import { h } from 'vue'

export default {
  props: ['logo'],

  inheritAttrs: false,

  render() {
    let fragment = document.createDocumentFragment()
    let span = document.createElement('span')
    span.innerHTML = this.$props.logo
    fragment.appendChild(span)
    const classes = this.$attrs.class.split(' ').filter(String)
    fragment.querySelector('svg').classList.add(...classes)

    return h('span', {
      innerHTML: span.innerHTML,
    })
  },
}
</script>
