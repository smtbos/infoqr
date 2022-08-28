package com.smtbos.infoqr;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

public class LoginActivity extends AppCompatActivity {

    private SharedPreferences sharedPreferences;
    private SharedPreferences.Editor editor;

    private RequestQueue requestQueue;

    private EditText txt_username, txt_password;
    private Button btn_login;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        sharedPreferences = getSharedPreferences("InfoQR", MODE_PRIVATE);
        editor = sharedPreferences.edit();

        requestQueue = Volley.newRequestQueue(this);

        txt_username = (EditText) findViewById(R.id.txt_username);
        txt_password = (EditText) findViewById(R.id.txt_password);
        btn_login = (Button) findViewById(R.id.btn_login);

        btn_login.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                        Request.Method.GET,
                        getLoginUrl(),
                        null,
                        new Response.Listener<JSONObject>() {
                            @Override
                            public void onResponse(JSONObject response) {
                                Log.d("TIMS", "onResponse: " + response.toString());
                                try {
                                    if (response.getBoolean("status") == true) {
                                        int u_id = response.getJSONObject("data").getInt("u_id");

                                        editor.putInt("u_id", u_id);
                                        editor.commit();

                                        showToast(response.getJSONArray("smsg").getString(0));

                                        Intent i = new Intent(LoginActivity.this, MainActivity.class);
                                        startActivity(i);
                                    } else {
                                        showToast(response.getJSONArray("emsg").getString(0));
                                    }
                                } catch (Exception e) {
                                    showToast("Failed to Process Request");
                                }
                            }
                        },
                        new Response.ErrorListener() {
                            @Override
                            public void onErrorResponse(VolleyError error) {
                                Log.e("TIMS", "onErrorResponse: " + error.toString());
                            }
                        }
                );
                requestQueue.add(jsonObjectRequest);
            }
        });
    }

    private String getLoginUrl() {
        String username = txt_username.getText().toString().trim();
        String password = txt_password.getText().toString().trim();

        Uri loginUri = Uri.parse(getString(R.string.API_ENDPOINT) + "users.php")
                .buildUpon()
                .appendQueryParameter("login", "1")
                .appendQueryParameter("username", username)
                .appendQueryParameter("password", password)
                .build();

        return loginUri.toString();
    }

    private void showToast(String s) {
        Toast.makeText(getApplicationContext(), s, Toast.LENGTH_SHORT).show();
    }
}