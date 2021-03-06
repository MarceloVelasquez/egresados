<template>
  <v-container grid-list-lg>
    <v-layout row wrap>
      <!-- Registro -->
      <v-flex xs12 md6>
        <v-form @submit.prevent="">
          <v-layout row wrap>
            <!-- Formulario -->
            <v-flex xs12>
              <v-card>
                <v-card-title class="title font-weight-light" primary-title>
                  Datos de postgrado
                </v-card-title>
                <v-card-text>
                  <section>
                    <v-select
                      :items="carreras"
                      v-model="carrera"
                      item-text="nombreEscuela"
                      item-value="codigo"
                      placeholder="Seleccione carrera"
                      label="Nombre de la escuela de la carrera"
                      prepend-icon="school"
                    ></v-select>
                    <v-select
                      v-model="tipo"
                      :items="tipos"
                      item-text="nombre"
                      item-value="codigo"
                      label="Seleccione tipo de postgrado"
                      placeholder="Tipo"
                      prepend-icon="laptop"
                      persistent-hint
                      :hint="descripcion"
                    ></v-select>
                    <v-radio-group v-model="lugar" row>
                      <v-radio
                        color="primary"
                        label="Universidad"
                        value="U"
                      ></v-radio>
                      <v-radio
                        color="primary"
                        label="Centro de estudios"
                        value="C"
                      ></v-radio>
                    </v-radio-group>
                    <v-combobox
                      v-if="lugar == 'U'"
                      v-model="universidad"
                      label="Seleccione universidad"
                      :items="universidades"
                      placeholder="Universidad"
                    ></v-combobox>
                    <v-combobox
                      v-else
                      v-model="centro"
                      label="Seleccione Centro de Estudios"
                      :items="centros"
                      placeholder="Centro de Estudios"
                    ></v-combobox>
                    <v-text-field
                      v-model="nombre"
                      label="Nombre de postgrado"
                    ></v-text-field>
                    <v-layout row wrap>
                      <v-flex xs6>
                        <v-menu
                          v-model="inicio"
                          lazy
                          offset-y
                          full-width
                          min-width="280px"
                          :close-on-content-click="false"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              v-model="fechaInicio"
                              label="Fecha de inicio"
                              prepend-icon="event"
                              readonly
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="fechaInicio"
                            @input="inicio = false"
                            locale="es-MX"
                            :show-current="false"
                            :max="new Date().toISOString().substr(0, 10)"
                          ></v-date-picker>
                        </v-menu>
                      </v-flex>
                      <v-flex xs6>
                        <v-menu
                          v-model="termino"
                          lazy
                          offset-y
                          full-width
                          min-width="280px"
                          :close-on-content-click="false"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              v-model="fechaTermino"
                              label="Fecha de término"
                              prepend-icon="event"
                              readonly
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="fechaTermino"
                            @input="termino = false"
                            locale="es-MX"
                            :show-current="false"
                            :min="fechaInicio"
                            :max="new Date().toISOString().substr(0, 10)"
                          ></v-date-picker>
                        </v-menu>
                      </v-flex>
                    </v-layout>
                    <v-select
                      :items="anios"
                      v-model="certificacion"
                      label="Año de certificación"
                      placeholder="Certificación"
                    ></v-select>
                  </section>
                </v-card-text>
              </v-card>
            </v-flex>
            <!-- Botones -->
            <v-btn
              color="primary"
              v-if="isEdit"
              @click="wait(editar)"
              type="submit"
            >
              Editar
            </v-btn>
            <v-btn color="primary" v-else @click="wait(agregar)" type="submit">
              Agregar
            </v-btn>
            <v-btn color="primary" outline @click="nuevo">Limpiar</v-btn>
          </v-layout>
        </v-form>
      </v-flex>
      <!-- Lista card -->
      <v-flex xs12 md6>
        <v-card v-if="lista.length != 0">
          <v-list three-line>
            <v-list-tile
              v-for="postgrado of lista"
              :key="postgrado.codigo"
              @click="copiarDatos(postgrado)"
            >
              <v-list-tile-content>
                <v-list-tile-title v-html="postgrado.nombre" />
                <v-list-tile-sub-title>
                  <span v-if="postgrado.lugar == 'U'">
                    {{ postgrado.universidad }}
                  </span>
                  <span else>{{ postgrado.razonSocial }}</span>
                  {{
                    `(${postgrado.fechaInicio.substring(0, 4)} - 
                    ${postgrado.fechaTermino.substring(0, 4)})`
                  }}
                </v-list-tile-sub-title>

                <v-list-tile-sub-title v-html="postgrado.tipo" />
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import { get, post, put } from "../../bd/api";
import { mapState, mapMutations } from "vuex";
export default {
  data: () => ({
    codigo: "",
    carreras: [],
    carrera: "",
    tipos: [],
    tipo: "",
    lugar: "U",
    universidades: [],
    universidad: "",
    centros: [],
    centro: "",
    nombre: "",
    fechaInicio: new Date().toISOString().substr(0, 10),
    inicio: false,
    fechaTermino: new Date().toISOString().substr(0, 10),
    termino: false,
    certificacion: "2019",
    lista: [],
    isEdit: false
  }),
  computed: {
    ...mapState(["user"]),
    descripcion() {
      if (this.tipo) {
        return this.tipos[this.tipo - 1].descripcion;
      }
    },
    anios() {
      let anios = [];
      let anioActual = new Date().getFullYear();
      for (let i = 0; i < 50; i++) {
        anios.push((anioActual - i).toString());
      }
      return anios;
    }
  },
  methods: {
    ...mapMutations(["snackbar"]),
    wait(callback) {
      setTimeout(callback, 1);
    },
    validar() {
      if (this.carrera == "") {
        this.snackbar("Ingrese nombre de la Carrera");
        return false;
      }
      if (this.tipo == "") {
        this.snackbar("Seleccione tipo de Postgrado");
        return false;
      }
      if (this.nombre == "") {
        this.snackbar("Ingrese nombre de Postgrado");
        return false;
      }
      if (this.lugar == "U") {
        if (this.universidad == "") {
          this.snackbar("Ingrese nombre de Universidad");
          return false;
        }
      } else {
        if (this.centro == "") {
          this.snackbar("Ingrese nombre de su Centro de estudios");
          return false;
        }
      }
      return true;
    },
    editar() {
      if (!this.validar()) return;
      let datos = {
        codigoEgresado: this.carrera,
        codigoTipo: this.tipo,
        nombre: this.nombre,
        fechaInicio: this.fechaInicio,
        fechaTermino: this.fechaTermino,
        anioCertificacion: this.certificacion
      };
      if (this.lugar == "U")
        datos = { ...datos, universidad: this.universidad };
      else datos = { ...datos, centroEstudios: this.centro };
      put("estudiosPostgrado/" + this.codigo, datos).then(res => {
        this.snackbar(res.mensaje);
        if (res.estado == true) {
          this.cargarTodo();
        }
      });
    },
    agregar() {
      if (!this.validar()) return;
      let datos = {
        codigoEgresado: this.carrera,
        codigoTipo: this.tipo,
        nombre: this.nombre,
        fechaInicio: this.fechaInicio,
        fechaTermino: this.fechaTermino,
        anioCertificacion: this.certificacion
      };
      if (this.lugar == "U")
        datos = { ...datos, universidad: this.universidad };
      else datos = { ...datos, centroEstudios: this.centro };

      post("estudiosPostgrado/add", datos).then(res => {
        this.snackbar(res.mensaje);
        if (res.estado == true) {
          this.cargarTodo();
          this.nuevo();
        }
      });
    },
    copiarDatos(postgrado) {
      this.isEdit = true;
      this.codigo = postgrado.codigo;
      this.carrera = postgrado.codigoEgresado;
      this.tipo = postgrado.codigoTipo;
      this.fechaInicio = postgrado.fechaInicio;
      this.fechaTermino = postgrado.fechaTermino;
      this.nombre = postgrado.nombre;
      this.certificacion = postgrado.anioCertificacion;
      if (postgrado.lugar == "U") {
        this.lugar = "U";
        this.universidad = postgrado.universidad;
        this.centro = "";
      } else {
        this.lugar = "C";
        this.centro = postgrado.razonSocial;
        this.universidad = "";
      }
    },
    nuevo() {
      this.isEdit = false;
      this.carrera = "";
      this.tipo = "";
      this.codigo = "";
      this.fechaInicio = new Date().toISOString();
      this.fechaTermino = new Date().toISOString();
      this.nombre = "";
      this.certificacion = "2019";
      this.lugar = "U";
      this.universidad = "";
      this.centro = "";
    },
    cargarTodo() {
      this.cargarUniversidades();
      this.cargarCentrosEstudio();
      this.cargarLista();
    },
    cargarTipos() {
      get("tiposPostgrado").then(res => (this.tipos = res.data));
    },
    cargarUniversidades() {
      get("universidades").then(res => (this.universidades = res.data));
    },
    cargarCentrosEstudio() {
      get("centroEstudios").then(res => (this.centros = res.data));
    },
    cargarLista() {
      get("estudiosPostgrado/" + this.user.dni).then(
        res => (this.lista = res.data)
      );
    },
    cargarCarreras() {
      get("carreras/" + this.user.dni).then(res => (this.carreras = res.data));
    }
  },
  created() {
    this.cargarTodo();
    this.cargarTipos();
    this.cargarCarreras();
  }
};
</script>
