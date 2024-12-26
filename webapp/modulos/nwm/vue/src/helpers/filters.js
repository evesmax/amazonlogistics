import axios from 'axios'

export function getActionPermission(action) {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'permisoAccion',
      action: action
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getWarehouses() {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'almacenes'
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getBranchOffices() {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'sucursales'
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getCustomers() {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'clientes'
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getProviders() {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'proveedores'
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getProducts() {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'productos'
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getUnitsMeasurement() {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'unidadesDeMedida'
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}

export function getSorters(pattern, sorter, department = 0, family = 0, line = 0) {
  return axios.get('./index.php', {
    params: {
      c: 'filters',
      f: 'buscarClasificadores',
      patron: pattern,
      clasificador: sorter,
      departamento: department,
      familia: family,
      linea: line,
    }
  })
    .then(res => {
      return res.data
    })
    .catch(error => {
      return []
    })
}
