import { url } from './config';
import store from '../store/index';

export async function get(peticion) {
  let res = await fetch(url + peticion);
  return res.json();
}

async function call(peticion, datos, method) {
  let res = await fetch(url + peticion, {
    method,
    body: JSON.stringify(datos),
    headers: { 'Content-Type': 'application/json' }
  });
  return res.json();
}

export async function post(peticion, datos) {
  return call(peticion, datos, 'POST');
}

export async function put(peticion, datos) {
  return call(peticion, datos, 'PUT');
}

export async function patch(peticion, datos) {
  return call(peticion, datos, 'PATCH');
}

export async function del(peticion, datos) {
  return call(peticion, datos, 'DELETE');
}

export async function uploadPhoto(peticion, foto) {
  let data = new FormData();
  data.append('profile', foto);
  let res = await fetch(url + peticion, {
    method: 'POST',
    body: data
  });
  return res.json();
}

export function hash(string) {
  return string.split('').reduce(function(a, b) {
    a = (a << 5) - a + b.charCodeAt(0);
    return a & a;
  }, 0);
}

export async function userFromServer() {
  let user = getUser();
  if (user == null) return 0;
  let res = await get('usuarios/' + user.dni);
  if (res.estado == true) setUser(res.data);
  return parseInt(res.data.vigencia);
}

export function getUser() {
  let localUser = localStorage.getItem('user') || '';
  let localHash = localStorage.getItem('hash');
  let mHash = hash(hash(localUser).toString()).toString();
  if (mHash == localHash) return JSON.parse(localUser);
  else return null;
}

export function setUser(data) {
  let user = JSON.stringify(data);
  localStorage.setItem('user', user);
  localStorage.setItem('hash', hash(hash(user).toString()));
  store.commit('updateUser');
}

export function removeUser() {
  localStorage.removeItem('user');
  localStorage.removeItem('hash');
  store.commit('updateUser');
}
