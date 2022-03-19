{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "automagistre.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Expand the name of the chart.
*/}}
{{- define "automagistre.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Full name
*/}}
{{- define "automagistre.fullname" -}}
{{- if .Values.fullnameOverride }}
{{- .Values.fullnameOverride | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- $name := default .Chart.Name .Values.nameOverride }}
{{- if contains $name .Release.Name }}
{{- .Release.Name | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}
{{- end }}

{{- define "automagistre.cron.fullname" -}}
{{- if .Values.cron.fullnameOverride -}}
{{- .Values.cron.fullnameOverride | trunc 63 | trimSuffix "-" -}}
{{- else -}}
{{- printf "%s-%s" .Release.Name "cron" -}}
{{- end }}
{{- end }}

{{- define "automagistre.messenger.fullname" -}}
{{- if .Values.messenger.fullnameOverride -}}
{{- .Values.messenger.fullnameOverride | trunc 63 | trimSuffix "-" -}}
{{- else -}}
{{- printf "%s-%s" .Release.Name "messenger" -}}
{{- end }}
{{- end }}

{{- define "automagistre.hasura.fullname" -}}
{{- if .Values.messenger.fullnameOverride -}}
{{- .Values.messenger.fullnameOverride | trunc 63 | trimSuffix "-" -}}
{{- else -}}
{{- printf "%s-%s" .Release.Name "hasura" -}}
{{- end }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "automagistre.commonLabels" -}}
helm.sh/chart: {{ include "automagistre.chart" . }}
helm.sh/release: {{ .Release.Name }}
app.kubernetes.io/name: {{ include "automagistre.fullname" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{/*
Selector labels
*/}}
{{- define "automagistre.selectorLabels" -}}
app.kubernetes.io/name: {{ include "automagistre.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}

{{- define "automagistre.cron.selectorLabels" -}}
app.kubernetes.io/component: cron
{{- end }}

{{- define "automagistre.messenger.selectorLabels" -}}
app.kubernetes.io/component: messenger
{{- end }}

{{- define "automagistre.hasura.selectorLabels" -}}
app.kubernetes.io/component: hasura
{{- end }}

{{/*
Create the name of the service account to use
*/}}
{{- define "automagistre.serviceAccountName" -}}
{{- if .Values.serviceAccount.create }}
{{- default (include "automagistre.fullname" .) .Values.serviceAccount.name }}
{{- else }}
{{- default "default" .Values.serviceAccount.name }}
{{- end }}
{{- end }}

{{- define "automagistre.envs" -}}
- name: APP_VERSION
  value: {{ .Chart.Version }}
- name: REDIS_HOST
  value: {{ .Values.externalRedis.host }}
- name: NSQ_HOST
  value: {{ .Values.externalNsq.host }}
- name: POSTGRES_HOST
  value: {{ .Values.externalPostgres.host }}
- name: POSTGRES_DATABASE
  value: {{ .Values.externalPostgres.database }}
- name: POSTGRES_USER
  value: {{ .Values.externalPostgres.user }}
{{- end -}}
