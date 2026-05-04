import React, { useState } from 'react';
import { View, Text, TextInput, Pressable, StyleSheet } from 'react-native';
import { apiRequest } from '../api/client';

export default function RegisterScreen() {
  const [form, setForm] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'volunteer'
  });
  const [message, setMessage] = useState('');

  const register = async () => {
    try {
      const data = await apiRequest('/register', {
        method: 'POST',
        body: JSON.stringify(form),
      });
      setMessage(data.message || 'Registered');
    } catch (e) {
      setMessage(e.message);
    }
  };

  return (
    <View style={styles.wrap}>
      <Text style={styles.title}>Register</Text>
      <TextInput style={styles.input} placeholder="Name" value={form.name} onChangeText={(v) => setForm({ ...form, name: v })} />
      <TextInput style={styles.input} placeholder="Email" value={form.email} onChangeText={(v) => setForm({ ...form, email: v })} autoCapitalize="none" />
      <TextInput style={styles.input} placeholder="Phone" value={form.phone} onChangeText={(v) => setForm({ ...form, phone: v })} />
      <TextInput style={styles.input} placeholder="Password" value={form.password} onChangeText={(v) => setForm({ ...form, password: v })} secureTextEntry />
      <Pressable style={styles.btn} onPress={register}><Text style={styles.btnText}>Create Account</Text></Pressable>
      {!!message && <Text style={styles.msg}>{message}</Text>}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { padding: 16 },
  title: { fontSize: 22, fontWeight: '700', marginBottom: 12 },
  input: { borderWidth: 1, borderColor: '#d1d5db', borderRadius: 8, padding: 10, marginBottom: 10, backgroundColor: '#fff' },
  btn: { backgroundColor: '#198754', borderRadius: 8, padding: 12, alignItems: 'center' },
  btnText: { color: '#fff', fontWeight: '700' },
  msg: { marginTop: 10 }
});
