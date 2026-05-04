import React, { useState } from 'react';
import { View, Text, TextInput, Pressable, StyleSheet } from 'react-native';
import { apiRequest } from '../api/client';

export default function LoginScreen({ onSuccess }) {
  const [email, setEmail] = useState('admin@humanitarian.local');
  const [password, setPassword] = useState('admin1234');
  const [message, setMessage] = useState('');

  const login = async () => {
    try {
      const data = await apiRequest('/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
      });
      setMessage(`Login success. Token: ${String(data.token || '').slice(0, 20)}...`);
      onSuccess?.();
    } catch (e) {
      setMessage(e.message);
    }
  };

  return (
    <View style={styles.wrap}>
      <Text style={styles.title}>Login</Text>
      <TextInput style={styles.input} value={email} onChangeText={setEmail} autoCapitalize="none" />
      <TextInput style={styles.input} value={password} onChangeText={setPassword} secureTextEntry />
      <Pressable style={styles.btn} onPress={login}><Text style={styles.btnText}>Sign In</Text></Pressable>
      {!!message && <Text style={styles.msg}>{message}</Text>}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { padding: 16 },
  title: { fontSize: 22, fontWeight: '700', marginBottom: 12 },
  input: { borderWidth: 1, borderColor: '#d1d5db', borderRadius: 8, padding: 10, marginBottom: 10, backgroundColor: '#fff' },
  btn: { backgroundColor: '#0b4f6c', borderRadius: 8, padding: 12, alignItems: 'center' },
  btnText: { color: '#fff', fontWeight: '700' },
  msg: { marginTop: 10 }
});
